<?php

namespace App\Services;

use App\Models\AffiliateCommission;
use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ReferralConversion;
use App\Models\ReferralCreditLedger;
use App\Models\Username;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    /**
     * Called when a customer places their first order.
     * Creates a ReferralConversion if they were referred by someone.
     */
    public function handleFirstOrder(Order $order): void
    {
        if (! AppSetting::bool('referral_enabled', false)) return;

        $customer = $order->customer;
        if (! $customer?->referred_by_username) return;

        // Guard: only fire once (no existing conversion for this customer)
        $alreadyConverted = ReferralConversion::where('referred_customer_id', $customer->id)->exists();
        if ($alreadyConverted) return;

        $referrerUsername = $customer->referred_by_username;
        $usernameRecord   = Username::find($referrerUsername);

        if (! $usernameRecord) {
            Log::warning("[Referral] Unknown referrer username: {$referrerUsername}");
            return;
        }

        $rewardAmount = (float) AppSetting::get('referral_default_amount', 0);
        if ($rewardAmount <= 0) return;

        $payoutType = AppSetting::get('referral_default_payout', 'credit');
        $autoTrigger = AppSetting::bool('referral_auto_trigger', true);

        DB::transaction(function () use (
            $referrerUsername, $usernameRecord, $customer, $order, $rewardAmount, $payoutType, $autoTrigger
        ) {
            $conversion = ReferralConversion::create([
                'referrer_username'    => $referrerUsername,
                'referrer_type'        => $usernameRecord->entity_type,
                'referrer_entity_id'   => $usernameRecord->entity_id,
                'referred_customer_id' => $customer->id,
                'order_id'             => $order->id,
                'reward_amount'        => $rewardAmount,
                'payout_type'          => $payoutType,
                'status'               => $autoTrigger ? 'pending' : 'pending',
            ]);

            if ($autoTrigger) {
                $this->processConversion($conversion);
            }
        });
    }

    /**
     * Process a pending conversion — credit the referrer's wallet or mark for bank transfer.
     */
    public function processConversion(ReferralConversion $conversion): void
    {
        if ($conversion->status !== 'pending') return;

        DB::transaction(function () use ($conversion) {
            if ($conversion->payout_type === 'credit') {
                ReferralCreditLedger::credit(
                    username:       $conversion->referrer_username,
                    ownerType:      $conversion->referrer_type,
                    ownerEntityId:  $conversion->referrer_entity_id,
                    amount:         (float) $conversion->reward_amount,
                    description:    "Referral reward for bringing in a new customer",
                    referenceType:  'referral_conversion',
                    referenceId:    $conversion->id,
                );

                $conversion->update(['status' => 'credited', 'processed_at' => now()]);
            } else {
                // Bank transfer — mark as pending for admin to process manually
                $conversion->update(['status' => 'pending', 'processed_at' => now()]);
            }
        });
    }

    /**
     * Called when a referred customer places any order (not just the first).
     * Creates an AffiliateCommission if the customer has a linked affiliate.
     */
    public function handleAffiliateCommission(Order $order): void
    {
        if (! AppSetting::bool('affiliate_enabled', false)) return;

        $customer = $order->customer;
        if (! $customer?->affiliate_id) return;

        $affiliate = $customer->affiliate;
        if (! $affiliate?->isActive()) return;

        // Avoid double-recording for the same order
        $exists = AffiliateCommission::where('order_id', $order->id)->exists();
        if ($exists) return;

        $rate   = $affiliate->effectiveCommissionRate();
        $amount = round((float) $order->total_amount * ($rate / 100), 2);

        if ($amount <= 0) return;

        $payoutType  = $affiliate->affiliate_payout_type;
        $autoApprove = AppSetting::bool('affiliate_auto_approve', false);

        DB::transaction(function () use ($affiliate, $customer, $order, $rate, $amount, $payoutType, $autoApprove) {
            $commission = AffiliateCommission::create([
                'affiliate_id'      => $affiliate->id,
                'customer_id'       => $customer->id,
                'order_id'          => $order->id,
                'order_amount'      => $order->total_amount,
                'commission_rate'   => $rate,
                'commission_amount' => $amount,
                'payout_type'       => $payoutType,
                'status'            => 'pending',
            ]);

            if ($autoApprove) {
                $this->approveCommission($commission);
            }
        });
    }

    /**
     * Admin approves a commission — credits wallet or marks for bank transfer.
     */
    public function approveCommission(AffiliateCommission $commission, ?int $approvedBy = null): void
    {
        if ($commission->status !== 'pending') return;

        DB::transaction(function () use ($commission, $approvedBy) {
            $commission->update([
                'status'      => 'approved',
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ]);

            if ($commission->payout_type === 'credit') {
                $affiliate = $commission->affiliate;

                ReferralCreditLedger::credit(
                    username:      $affiliate->username,
                    ownerType:     'affiliate',
                    ownerEntityId: $affiliate->id,
                    amount:        (float) $commission->commission_amount,
                    description:   "Affiliate commission ({$commission->commission_rate}%) on order #{$commission->order_id}",
                    referenceType: 'affiliate_commission',
                    referenceId:   $commission->id,
                );
            }
        });
    }

    /**
     * Apply referral credit to an order (debit the wallet).
     * Returns the amount actually applied (capped at order total).
     */
    public function applyCredit(Customer $customer, Order $order, float $requestedAmount): float
    {
        if (! $customer->username) return 0.0;

        $balance = $customer->creditBalance();
        $apply   = min($requestedAmount, $balance, (float) $order->total_amount);

        if ($apply <= 0) return 0.0;

        $usernameRecord = Username::find($customer->username);

        DB::transaction(function () use ($customer, $order, $apply, $usernameRecord) {
            ReferralCreditLedger::debit(
                username:      $customer->username,
                ownerType:     'customer',
                ownerEntityId: $customer->id,
                amount:        $apply,
                description:   "Credit applied to order {$order->reference}",
                referenceType: 'order',
                referenceId:   $order->id,
            );

            $order->update(['referral_credit_used' => $apply]);
        });

        return $apply;
    }
}
