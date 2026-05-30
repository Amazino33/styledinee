<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:2rem 1rem;">
<div style="width:100%;max-width:420px;">

    {{-- Logo --}}
    <div style="text-align:center;margin-bottom:2rem;">
        <div style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:600;letter-spacing:.06em;">
            STYLE<span style="color:var(--gold)">DINEE</span>
        </div>
        <div style="font-size:.85rem;color:var(--text-muted);margin-top:.5rem;">
            @if($mode === 'password') Sign in with your password
            @elseif($step === 'otp') Enter the code we sent you
            @else Sign in to your account
            @endif
        </div>
    </div>

    {{-- Mode toggle --}}
    @if($step === 'phone' || $mode === 'password')
    <div style="display:flex;border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem;">
        <button type="button"
            wire:click="$set('mode','otp')"
            style="flex:1;padding:.55rem;font-size:.82rem;font-weight:600;border:none;cursor:pointer;
                   background:{{ $mode==='otp' ? 'var(--black)' : 'transparent' }};
                   color:{{ $mode==='otp' ? '#fff' : 'var(--text-muted)' }};">
            WhatsApp / SMS Code
        </button>
        <button type="button"
            wire:click="$set('mode','password')"
            style="flex:1;padding:.55rem;font-size:.82rem;font-weight:600;border:none;cursor:pointer;
                   background:{{ $mode==='password' ? 'var(--black)' : 'transparent' }};
                   color:{{ $mode==='password' ? '#fff' : 'var(--text-muted)' }};">
            Password
        </button>
    </div>
    @endif

    <div class="card" style="padding:2rem;">

        @if($error)
        <div style="background:#FEF2F2;border:1px solid #FECACA;color:#991B1B;padding:.75rem 1rem;border-radius:8px;font-size:.85rem;margin-bottom:1.25rem;">
            {{ $error }}
        </div>
        @endif

        {{-- ── OTP: Phone step ────────────────────────────── --}}
        @if($mode === 'otp' && $step === 'phone')
        <form wire:submit="requestOtp">
            @if($isNew ?? false)
            <div class="field">
                <label class="field__label">Full Name</label>
                <input class="field__input" type="text" wire:model="name" placeholder="Amara Obi" autocomplete="name">
                @error('name') <span class="field__error">{{ $message }}</span> @enderror
            </div>
            @endif

            <div class="field">
                <label class="field__label">Phone Number</label>
                <input class="field__input" type="tel" wire:model="phone" placeholder="08012345678" autocomplete="tel" autofocus>
                @error('phone') <span class="field__error">{{ $message }}</span> @enderror
                <span class="hint">We'll send a 6-digit code to your WhatsApp or SMS.</span>
            </div>

            @if($referred_by)
            <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:1rem;padding:.6rem 1rem;background:#f9f9f9;border-radius:8px;">
                Referral code applied: <strong>{{ $referred_by }}</strong>
            </div>
            @endif

            <button type="submit" class="btn btn--gold" style="width:100%;justify-content:center;" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="requestOtp">Send Code</span>
                <span wire:loading wire:target="requestOtp">Sending…</span>
            </button>
        </form>
        @endif

        {{-- ── OTP: Code step ──────────────────────────────── --}}
        @if($mode === 'otp' && $step === 'otp')
        <form wire:submit="verifyOtp">
            <div style="text-align:center;margin-bottom:1.5rem;">
                <div style="font-size:.9rem;color:var(--text-muted);">
                    Code sent to <strong>{{ $phone }}</strong>
                </div>
            </div>

            <div class="field">
                <label class="field__label">6-Digit Code</label>
                <input class="field__input" type="text" wire:model="otp" placeholder="000000" maxlength="6"
                    inputmode="numeric" autocomplete="one-time-code"
                    style="letter-spacing:.3em;font-size:1.4rem;text-align:center;" autofocus>
                @error('otp') <span class="field__error">{{ $message }}</span> @enderror
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

        {{-- ── Password login ───────────────────────────────── --}}
        @if($mode === 'password')
        <form wire:submit="loginWithPassword">
            <div class="field">
                <label class="field__label">Phone Number</label>
                <input class="field__input" type="tel" wire:model="pw_phone" placeholder="08012345678" autocomplete="tel" autofocus>
                @error('pw_phone') <span class="field__error">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label class="field__label">Password</label>
                <input class="field__input" type="password" wire:model="pw_password" autocomplete="current-password">
                @error('pw_password') <span class="field__error">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn--gold" style="width:100%;justify-content:center;" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="loginWithPassword">Sign In</span>
                <span wire:loading wire:target="loginWithPassword">Signing in…</span>
            </button>

            <p style="font-size:.78rem;color:var(--text-muted);text-align:center;margin-top:.85rem;">
                No password set? Switch to <strong>WhatsApp / SMS Code</strong> above.
            </p>
        </form>
        @endif

    </div>

    <p style="text-align:center;font-size:.78rem;color:var(--text-muted);margin-top:1.25rem;">
        <a href="{{ url('/') }}" style="color:var(--text-muted);text-decoration:underline;text-underline-offset:3px;">← Back to website</a>
    </p>
</div>
</div>
