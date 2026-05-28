<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CouponService
{
    /**
     * Validate a coupon code for a given order total and optional customer.
     * Returns an array with 'valid', 'discount', and 'message'.
     */
    public function validate(string $code, float $orderTotal, ?Customer $customer = null): array
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (! $coupon) {
            return ['valid' => false, 'discount' => 0, 'message' => 'Coupon not found.'];
        }

        if (! $coupon->isCurrentlyActive()) {
            return ['valid' => false, 'discount' => 0, 'message' => 'This coupon is no longer active.'];
        }

        if ($coupon->min_order_amount && $orderTotal < $coupon->min_order_amount) {
            return [
                'valid'    => false,
                'discount' => 0,
                'message'  => "Minimum order of ₦" . number_format($coupon->min_order_amount, 2) . " required.",
            ];
        }

        if ($customer) {
            if (! $coupon->isEligibleFor($customer)) {
                return ['valid' => false, 'discount' => 0, 'message' => 'You are not eligible for this coupon.'];
            }

            if ($coupon->usage_limit_per_customer) {
                $used = CouponUsage::where('coupon_id', $coupon->id)
                    ->where('customer_id', $customer->id)
                    ->count();

                if ($used >= $coupon->usage_limit_per_customer) {
                    return ['valid' => false, 'discount' => 0, 'message' => 'You have already used this coupon.'];
                }
            }
        }

        $discount = $coupon->calculateDiscount($orderTotal);

        return [
            'valid'    => true,
            'discount' => $discount,
            'coupon'   => $coupon,
            'message'  => "Coupon applied. Discount: ₦" . number_format($discount, 2),
        ];
    }

    /**
     * Apply a coupon to an order — records usage and updates order.
     * Call this only after validate() confirms valid.
     */
    public function apply(Coupon $coupon, Order $order, float $discount, ?Customer $customer = null): void
    {
        DB::transaction(function () use ($coupon, $order, $discount, $customer) {
            CouponUsage::create([
                'coupon_id'       => $coupon->id,
                'customer_id'     => $customer?->id,
                'order_id'        => $order->id,
                'discount_amount' => $discount,
            ]);

            $coupon->increment('used_count');

            $order->update([
                'coupon_id'       => $coupon->id,
                'coupon_discount' => $discount,
            ]);
        });
    }
}
