<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use App\Services\WhatsAppService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AdminSettings extends Page
{
    protected string $view = 'filament.pages.admin-settings';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $title           = 'Admin Settings';
    protected static ?int    $navigationSort  = 20;

    public static function getNavigationIcon(): string   { return 'heroicon-o-cog-6-tooth'; }
    public static function getNavigationGroup(): ?string { return 'Settings'; }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user instanceof \App\Models\User && $user->hasRole('admin');
    }

    // ── Active tab ──────────────────────────────────────────────────────────────
    public string $tab = 'payment';

    // ── Payment settings ────────────────────────────────────────────────────────
    public string $payment_policy  = 'half_upfront';
    public int    $deposit_percent = 50;

    // ── WhatsApp settings ───────────────────────────────────────────────────────
    public string $instance_id   = '';
    public string $access_token  = '';
    public bool   $enabled       = false;
    public string $test_number   = '';

    // ── SMS fallback ────────────────────────────────────────────────────────────
    public bool   $sms_enabled    = false;
    public string $sms_provider   = 'termii';
    public string $sms_api_key    = '';
    public string $sms_api_secret = '';
    public string $sms_sender_id  = 'Styledinee';

    // ── OTP rate limiting ───────────────────────────────────────────────────────
    public int $otp_window_minutes = 10;
    public int $otp_max_attempts   = 3;

    // ── Referral settings ───────────────────────────────────────────────────────
    public bool   $referral_enabled          = false;
    public string $referral_default_amount   = '';
    public string $referral_min_order_amount = '0';
    public string $referral_default_payout   = 'credit';
    public bool   $referral_auto_trigger     = true;

    // ── Affiliate settings ──────────────────────────────────────────────────────
    public bool   $affiliate_enabled           = false;
    public string $affiliate_default_rate      = '';
    public string $affiliate_min_order_amount  = '0';
    public string $affiliate_default_payout    = 'bank_transfer';
    public bool   $affiliate_auto_approve      = false;
    public bool   $affiliate_registration_open = true;

    // ── Credit settings ─────────────────────────────────────────────────────────
    public bool $credit_auto_apply = true;

    // ── POS settings ────────────────────────────────────────────────────────────
    public string $bom_mode = 'full';

    public function mount(): void
    {
        // Payment
        $this->payment_policy  = AppSetting::get('payment_policy', 'half_upfront');
        $this->deposit_percent = (int) AppSetting::get('deposit_percent', 50);

        // WhatsApp
        $this->instance_id  = AppSetting::get('wawp_instance_id', '');
        $this->access_token = AppSetting::get('wawp_access_token', '');
        $this->enabled      = AppSetting::bool('wawp_enabled', false);

        // SMS
        $this->sms_enabled    = AppSetting::bool('sms_enabled', false);
        $this->sms_provider   = AppSetting::get('sms_provider', 'termii');
        $this->sms_api_key    = AppSetting::get('sms_api_key', '');
        $this->sms_api_secret = AppSetting::get('sms_api_secret', '');
        $this->sms_sender_id  = AppSetting::get('sms_sender_id', 'Styledinee');

        // OTP
        $this->otp_window_minutes = (int) AppSetting::get('otp_window_minutes', 10);
        $this->otp_max_attempts   = (int) AppSetting::get('otp_max_attempts', 3);

        // Referral
        $this->referral_enabled          = AppSetting::bool('referral_enabled', false);
        $this->referral_default_amount   = AppSetting::get('referral_default_amount', '0');
        $this->referral_min_order_amount = AppSetting::get('referral_min_order_amount', '0');
        $this->referral_default_payout   = AppSetting::get('referral_default_payout', 'credit');
        $this->referral_auto_trigger     = AppSetting::bool('referral_auto_trigger', true);

        // Affiliate
        $this->affiliate_enabled           = AppSetting::bool('affiliate_enabled', false);
        $this->affiliate_default_rate      = AppSetting::get('affiliate_default_rate', '5');
        $this->affiliate_min_order_amount  = AppSetting::get('affiliate_min_order_amount', '0');
        $this->affiliate_default_payout    = AppSetting::get('affiliate_default_payout', 'bank_transfer');
        $this->affiliate_auto_approve      = AppSetting::bool('affiliate_auto_approve', false);
        $this->affiliate_registration_open = AppSetting::bool('affiliate_registration_open', true);

        // Credit
        $this->credit_auto_apply = AppSetting::bool('credit_auto_apply', true);

        // POS
        $this->bom_mode = AppSetting::get('bom_mode', 'full');
    }

    public function savePayment(): void
    {
        $this->validate([
            'payment_policy'  => ['required', 'in:half_upfront,pay_later'],
            'deposit_percent' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        AppSetting::set('payment_policy',  $this->payment_policy);
        AppSetting::set('deposit_percent', (string) $this->deposit_percent);

        Notification::make()->title('Payment settings saved.')->success()->send();
    }

    public function saveMessaging(): void
    {
        $this->validate([
            'instance_id'        => ['nullable', 'string', 'max:100'],
            'access_token'       => ['nullable', 'string', 'max:200'],
            'sms_api_key'        => ['nullable', 'string', 'max:200'],
            'sms_api_secret'     => ['nullable', 'string', 'max:200'],
            'sms_sender_id'      => ['nullable', 'string', 'max:20'],
            'otp_window_minutes' => ['required', 'integer', 'min:1', 'max:60'],
            'otp_max_attempts'   => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        AppSetting::set('wawp_instance_id',  trim($this->instance_id));
        AppSetting::set('wawp_access_token', trim($this->access_token));
        AppSetting::set('wawp_enabled',      $this->enabled ? '1' : '0');

        AppSetting::set('sms_enabled',    $this->sms_enabled ? '1' : '0');
        AppSetting::set('sms_provider',   $this->sms_provider);
        AppSetting::set('sms_api_key',    $this->sms_api_key);
        AppSetting::set('sms_api_secret', $this->sms_api_secret);
        AppSetting::set('sms_sender_id',  $this->sms_sender_id);

        AppSetting::set('otp_window_minutes', (string) $this->otp_window_minutes);
        AppSetting::set('otp_max_attempts',   (string) $this->otp_max_attempts);

        Notification::make()->title('Messaging settings saved.')->success()->send();
    }

    public function sendTest(): void
    {
        $this->validate(['test_number' => ['required', 'string', 'min:7']]);

        $sent = app(WhatsAppService::class)->send(
            $this->test_number,
            'This is a test message from Styledinee. Your messaging integration is working correctly.'
        );

        if ($sent) {
            Notification::make()->title('Test message sent.')->success()->send();
        } else {
            Notification::make()
                ->title('Not sent — check log for details.')
                ->body('Enable WhatsApp or SMS and save credentials first.')
                ->warning()->send();
        }
    }

    public function saveReferral(): void
    {
        $this->validate([
            'referral_default_amount'  => ['required', 'numeric', 'min:0'],
            'referral_default_payout'  => ['required', 'in:credit,bank_transfer'],
            'affiliate_default_rate'   => ['required', 'numeric', 'min:0', 'max:100'],
            'affiliate_default_payout' => ['required', 'in:bank_transfer,credit'],
        ]);

        AppSetting::set('referral_enabled',          $this->referral_enabled ? '1' : '0');
        AppSetting::set('referral_default_amount',   $this->referral_default_amount);
        AppSetting::set('referral_min_order_amount', $this->referral_min_order_amount);
        AppSetting::set('referral_default_payout',   $this->referral_default_payout);
        AppSetting::set('referral_auto_trigger',     $this->referral_auto_trigger ? '1' : '0');

        AppSetting::set('affiliate_enabled',           $this->affiliate_enabled ? '1' : '0');
        AppSetting::set('affiliate_default_rate',      $this->affiliate_default_rate);
        AppSetting::set('affiliate_min_order_amount',  $this->affiliate_min_order_amount);
        AppSetting::set('affiliate_default_payout',    $this->affiliate_default_payout);
        AppSetting::set('affiliate_auto_approve',      $this->affiliate_auto_approve ? '1' : '0');
        AppSetting::set('affiliate_registration_open', $this->affiliate_registration_open ? '1' : '0');

        AppSetting::set('credit_auto_apply', $this->credit_auto_apply ? '1' : '0');

        Notification::make()->title('Referral & Affiliate settings saved.')->success()->send();
    }

    public function savePos(): void
    {
        $this->validate([
            'bom_mode' => ['required', 'in:full,remove_only,view_only,disabled'],
        ]);

        AppSetting::set('bom_mode', $this->bom_mode);

        Notification::make()->title('POS settings saved.')->success()->send();
    }
}
