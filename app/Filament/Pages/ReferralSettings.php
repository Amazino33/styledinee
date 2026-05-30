<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ReferralSettings extends Page
{
    protected string $view = 'filament.pages.referral-settings';
    protected static ?string $navigationLabel = 'Referral & Affiliate';
    protected static ?string $title = 'Referral & Affiliate Settings';
    protected static ?int $navigationSort = 15;

    public static function getNavigationIcon(): string { return 'heroicon-o-adjustments-horizontal'; }
    public static function getNavigationGroup(): ?string { return 'Referral & Rewards'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    // ── Referral settings ──────────────────────────────────────────────────────
    public bool   $referral_enabled           = false;
    public string $referral_default_amount    = '';
    public string $referral_min_order_amount  = '0';
    public string $referral_default_payout    = 'credit';
    public bool   $referral_auto_trigger      = true;

    // ── Affiliate settings ─────────────────────────────────────────────────────
    public bool   $affiliate_enabled              = false;
    public string $affiliate_default_rate         = '';
    public string $affiliate_min_order_amount     = '0';
    public string $affiliate_default_payout       = 'bank_transfer';
    public bool   $affiliate_auto_approve         = false;
    public bool   $affiliate_registration_open    = true;

    // ── Credit settings ────────────────────────────────────────────────────────
    public bool $credit_auto_apply = true;

    public function mount(): void
    {
        $this->referral_enabled           = AppSetting::bool('referral_enabled', false);
        $this->referral_default_amount    = AppSetting::get('referral_default_amount', '0');
        $this->referral_min_order_amount  = AppSetting::get('referral_min_order_amount', '0');
        $this->referral_default_payout    = AppSetting::get('referral_default_payout', 'credit');
        $this->referral_auto_trigger      = AppSetting::bool('referral_auto_trigger', true);

        $this->affiliate_enabled              = AppSetting::bool('affiliate_enabled', false);
        $this->affiliate_default_rate         = AppSetting::get('affiliate_default_rate', '5');
        $this->affiliate_min_order_amount     = AppSetting::get('affiliate_min_order_amount', '0');
        $this->affiliate_default_payout       = AppSetting::get('affiliate_default_payout', 'bank_transfer');
        $this->affiliate_auto_approve         = AppSetting::bool('affiliate_auto_approve', false);
        $this->affiliate_registration_open    = AppSetting::bool('affiliate_registration_open', true);

        $this->credit_auto_apply = AppSetting::bool('credit_auto_apply', true);
    }

    public function save(): void
    {
        $this->validate([
            'referral_default_amount' => ['required', 'numeric', 'min:0'],
            'referral_default_payout' => ['required', 'in:credit,bank_transfer'],
            'affiliate_default_rate'  => ['required', 'numeric', 'min:0', 'max:100'],
            'affiliate_default_payout' => ['required', 'in:bank_transfer,credit'],
        ]);

        AppSetting::set('referral_enabled',          $this->referral_enabled ? '1' : '0');
        AppSetting::set('referral_default_amount',   $this->referral_default_amount);
        AppSetting::set('referral_min_order_amount', $this->referral_min_order_amount);
        AppSetting::set('referral_default_payout',   $this->referral_default_payout);
        AppSetting::set('referral_auto_trigger',     $this->referral_auto_trigger ? '1' : '0');

        AppSetting::set('affiliate_enabled',              $this->affiliate_enabled ? '1' : '0');
        AppSetting::set('affiliate_default_rate',         $this->affiliate_default_rate);
        AppSetting::set('affiliate_min_order_amount',     $this->affiliate_min_order_amount);
        AppSetting::set('affiliate_default_payout',       $this->affiliate_default_payout);
        AppSetting::set('affiliate_auto_approve',         $this->affiliate_auto_approve ? '1' : '0');
        AppSetting::set('affiliate_registration_open',    $this->affiliate_registration_open ? '1' : '0');

        AppSetting::set('credit_auto_apply', $this->credit_auto_apply ? '1' : '0');

        Notification::make()->title('Settings saved.')->success()->send();
    }
}
