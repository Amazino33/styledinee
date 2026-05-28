<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:2rem 1rem;">
<div style="width:100%;max-width:420px;">

    {{-- Logo --}}
    <div style="text-align:center;margin-bottom:2rem;">
        <div style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:600;letter-spacing:.06em;">
            STYLE<span style="color:var(--gold)">DINEE</span>
        </div>
        <div style="font-size:.85rem;color:var(--text-muted);margin-top:.5rem;">
            @if($step === 'phone') Sign in to your account @endif
            @if($step === 'otp')   Enter the code we sent you @endif
            @if($step === 'username') Set up your account @endif
        </div>
    </div>

    <div class="card" style="padding:2rem;">

        @if($error)
        <div style="background:#FEF2F2;border:1px solid #FECACA;color:#991B1B;padding:.75rem 1rem;border-radius:8px;font-size:.85rem;margin-bottom:1.25rem;">
            {{ $error }}
        </div>
        @endif

        {{-- Step 1: Phone ─────────────────────────────────── --}}
        @if($step === 'phone')
        <form wire:submit="requestOtp">
            @if($isNew ?? false)
            <div class="field">
                <label>Full Name</label>
                <input type="text" wire:model="name" placeholder="Amara Obi" autocomplete="name">
                @error('name') <span class="err">{{ $message }}</span> @enderror
            </div>
            @endif

            <div class="field">
                <label>Phone Number</label>
                <input type="tel" wire:model="phone" placeholder="08012345678" autocomplete="tel" autofocus>
                @error('phone') <span class="err">{{ $message }}</span> @enderror
                <span class="hint">We'll send a 6-digit code to your WhatsApp.</span>
            </div>

            @if($referred_by)
            <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:1rem;padding:.6rem 1rem;background:var(--gray-100);border-radius:8px;">
                Referral code applied: <strong>{{ $referred_by }}</strong>
            </div>
            @endif

            <button type="submit" class="btn btn--gold" style="width:100%;justify-content:center;" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="requestOtp">Send Code</span>
                <span wire:loading wire:target="requestOtp">Sending…</span>
            </button>
        </form>
        @endif

        {{-- Step 2: OTP ──────────────────────────────────── --}}
        @if($step === 'otp')
        <form wire:submit="verifyOtp">
            <div style="text-align:center;margin-bottom:1.5rem;">
                <div style="font-size:.9rem;color:var(--text-muted);">
                    Code sent to <strong>{{ $phone }}</strong>
                </div>
            </div>

            <div class="field">
                <label>6-Digit Code</label>
                <input type="text" wire:model="otp" placeholder="000000" maxlength="6"
                    inputmode="numeric" autocomplete="one-time-code"
                    style="letter-spacing:.3em;font-size:1.4rem;text-align:center;" autofocus>
                @error('otp') <span class="err">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn--gold" style="width:100%;justify-content:center;" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="verifyOtp">Verify Code</span>
                <span wire:loading wire:target="verifyOtp">Verifying…</span>
            </button>

            <button type="button" wire:click="resendOtp"
                style="width:100%;text-align:center;margin-top:.75rem;background:none;border:none;color:var(--text-muted);font-size:.8rem;cursor:pointer;font-family:'Jost',sans-serif;">
                ← Use a different number
            </button>
        </form>
        @endif

        {{-- Step 3: Username ────────────────────────────────── --}}
        @if($step === 'username')
        <form wire:submit="setUsername">
            <p style="font-size:.88rem;color:var(--text-muted);margin-bottom:1.25rem;">
                Almost there — choose a unique username. It becomes your referral code.
            </p>

            @if(!$name)
            <div class="field">
                <label>Full Name</label>
                <input type="text" wire:model="name" placeholder="Amara Obi" autocomplete="name" autofocus>
                @error('name') <span class="err">{{ $message }}</span> @enderror
            </div>
            @endif

            <div class="field">
                <label>Username</label>
                <input type="text" wire:model="username" placeholder="amara" autocomplete="off"
                    style="font-family:monospace;" {{ $name ? 'autofocus' : '' }}>
                @error('username') <span class="err">{{ $message }}</span> @enderror
                <span class="hint">Letters, numbers, underscores only. This becomes your referral code.</span>
            </div>

            <button type="submit" class="btn btn--gold" style="width:100%;justify-content:center;" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="setUsername">Continue</span>
                <span wire:loading wire:target="setUsername">Saving…</span>
            </button>
        </form>
        @endif

    </div>

    <p style="text-align:center;font-size:.78rem;color:var(--text-muted);margin-top:1.25rem;">
        <a href="{{ url('/') }}" style="color:var(--text-muted);text-decoration:underline;text-underline-offset:3px;">← Back to website</a>
    </p>
</div>
</div>
