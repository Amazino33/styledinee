<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Models\Username;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Login extends Component
{
    public string $step       = 'phone'; // phone | otp
    public string $phone      = '';
    public string $otp        = '';
    public string $name       = '';
    public string $referred_by = '';

    public bool $sending  = false;
    public bool $isNew    = false;
    public ?string $error = null;

    public function mount(): void
    {
        // Pre-fill referral code if coming from a referral link
        $this->referred_by = request()->query('ref', '');

        if (Auth::guard('customer_web')->check()) {
            $this->redirect(route('account.dashboard'));
        }
    }

    public function requestOtp(): void
    {
        $this->validate(['phone' => ['required', 'string', 'min:7']]);
        $this->error = null;

        $normalized = Customer::normalizePhone($this->phone);

        // Expire old OTPs
        CustomerOtp::where('phone', $normalized)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->update(['expires_at' => now()]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        CustomerOtp::create([
            'phone'      => $normalized,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        app(NotificationService::class)->sendAuthOtp($normalized, $otp);

        $this->isNew = ! Customer::where('phone', $normalized)->exists();
        $this->step  = 'otp';
    }

    public function verifyOtp(): void
    {
        $this->validate(['otp' => ['required', 'string', 'size:6']]);
        $this->error = null;

        $normalized = Customer::normalizePhone($this->phone);

        $record = CustomerOtp::where('phone', $normalized)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record || $record->otp !== $this->otp) {
            $this->error = 'Invalid or expired OTP. Please check your WhatsApp.';
            return;
        }

        $record->update(['verified_at' => now()]);

        $customer = DB::transaction(function () use ($normalized) {
            $existing = Customer::where('phone', $normalized)->first();
            if ($existing) return $existing;

            // Validate referral code
            $referredBy = null;
            if ($this->referred_by && Username::isAvailable($this->referred_by) === false) {
                $referredBy = $this->referred_by;
            }

            return Customer::create([
                'phone'                => $normalized,
                'name'                 => $this->name ?: 'Customer',
                'referred_by_username' => $referredBy,
            ]);
        });

        Auth::guard('customer_web')->login($customer);

        // Always do a full redirect after login so the new session's
        // CSRF token is loaded into the page before any further Livewire requests.
        $this->redirect(route('account.dashboard'));
    }

    public function resendOtp(): void
    {
        $this->step  = 'phone';
        $this->otp   = '';
        $this->error = null;
    }

    public function render()
    {
        return view('livewire.customer.login')
            ->layout('layouts.customer')
            ->title('Sign In');
    }
}
