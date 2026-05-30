<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Models\Username;
use App\Services\NotificationService;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Login extends Component
{
    public string $mode = 'otp';   // 'otp' | 'password'

    // OTP flow
    public string $step       = 'phone'; // phone | otp
    public string $phone      = '';
    public string $otp        = '';
    public string $name       = '';
    public string $referred_by = '';
    public bool   $isNew      = false;

    // Password flow
    public string $pw_phone    = '';
    public string $pw_password = '';

    public ?string $error = null;

    public function mount(): void
    {
        $this->referred_by = request()->query('ref', '');

        if (Auth::guard('customer_web')->check()) {
            $this->redirect(route('account.dashboard'));
        }
    }

    // ── OTP flow ───────────────────────────────────────────────────────────────

    public function requestOtp(): void
    {
        $this->validate(['phone' => ['required', 'string', 'min:7']]);
        $this->error = null;

        $normalized = Customer::normalizePhone($this->phone);

        // Rate limit check
        if (! app(WhatsAppService::class)->checkOtpRateLimit($normalized)) {
            $this->error = 'Too many code requests. Please wait a few minutes before trying again.';
            return;
        }

        // Expire previous active OTPs for this number
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
            $this->error = 'Invalid or expired code. Please check your WhatsApp or SMS.';
            return;
        }

        $record->update(['verified_at' => now()]);

        $customer = DB::transaction(function () use ($normalized) {
            $existing = Customer::where('phone', $normalized)->first();
            if ($existing) return $existing;

            $referredBy = null;
            if ($this->referred_by && ! Username::isAvailable($this->referred_by)) {
                $referredBy = $this->referred_by;
            }

            return Customer::create([
                'phone'                => $normalized,
                'name'                 => $this->name ?: 'Customer',
                'referred_by_username' => $referredBy,
            ]);
        });

        Auth::guard('customer_web')->login($customer);
        $this->redirect(route('account.dashboard'));
    }

    public function resendOtp(): void
    {
        $this->step  = 'phone';
        $this->otp   = '';
        $this->error = null;
    }

    // ── Password flow ──────────────────────────────────────────────────────────

    public function loginWithPassword(): void
    {
        $this->validate([
            'pw_phone'    => ['required', 'string', 'min:7'],
            'pw_password' => ['required', 'string'],
        ]);

        $this->error = null;

        $normalized = Customer::normalizePhone($this->pw_phone);
        $customer   = Customer::where('phone', $normalized)->first();

        if (! $customer || ! $customer->password || ! Hash::check($this->pw_password, $customer->password)) {
            $this->error = 'Incorrect phone number or password.';
            return;
        }

        Auth::guard('customer_web')->login($customer);
        $this->redirect(route('account.dashboard'));
    }

    // ── Render ─────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.customer.login')
            ->layout('layouts.customer')
            ->title('Sign In');
    }
}
