<div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:.75rem;">
        <div>
            <h1 class="page-title" style="margin-bottom:.1rem;">My Profile</h1>
            <p class="page-subtitle" style="margin:0;">Update your contact details and password.</p>
        </div>
        <a href="{{ route('account.dashboard') }}" class="btn btn--outline btn--sm">
            ← Back to Dashboard
        </a>
    </div>

    {{-- ── Profile details ────────────────────────────────── --}}
    <div class="card" style="margin-bottom:1.25rem;">
        <div class="section-head" style="margin-bottom:1.25rem;">
            <h2>Personal Details</h2>
        </div>

        @if($saved)
        <div style="background:#f0faf4;border:1px solid #a8dbb8;color:#2d6a4f;padding:.75rem 1rem;border-radius:.5rem;margin-bottom:1.25rem;font-size:.9rem;">
            Profile saved successfully.
        </div>
        @endif

        @if($username)
        <div class="field" style="margin-bottom:1.25rem;">
            <label class="field__label">Referral Code (Username)</label>
            <div style="display:flex;align-items:center;gap:.75rem;">
                <div class="referral-code" style="font-size:1rem;padding:.45rem .9rem;">{{ $username }}</div>
                <span style="font-size:.75rem;color:var(--text-muted);">Cannot be changed</span>
            </div>
        </div>
        @endif

        <form wire:submit.prevent="save">
            <div class="field">
                <label class="field__label" for="pf-name">Full Name</label>
                <input class="field__input" id="pf-name" type="text" wire:model="name" required>
                @error('name')<span class="field__error">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label class="field__label" for="pf-email">Email Address <span style="color:var(--text-muted);font-weight:400;">(optional)</span></label>
                <input class="field__input" id="pf-email" type="email" wire:model="email" placeholder="you@example.com">
                @error('email')<span class="field__error">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label class="field__label" for="pf-address">Delivery Address <span style="color:var(--text-muted);font-weight:400;">(optional)</span></label>
                <textarea class="field__input" id="pf-address" wire:model="address" rows="3" placeholder="Street, City, State"></textarea>
                @error('address')<span class="field__error">{{ $message }}</span>@enderror
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:.5rem;">
                <button type="submit" class="btn btn--primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Save Changes</span>
                    <span wire:loading>Saving…</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ── Change password ─────────────────────────────────── --}}
    <div class="card">
        <div class="section-head" style="margin-bottom:1.25rem;">
            <h2>Change Password</h2>
        </div>

        @if($passwordSaved)
        <div style="background:#f0faf4;border:1px solid #a8dbb8;color:#2d6a4f;padding:.75rem 1rem;border-radius:.5rem;margin-bottom:1.25rem;font-size:.9rem;">
            Password updated successfully.
        </div>
        @endif

        @if($passwordError)
        <div style="background:#FEF2F2;border:1px solid #FECACA;color:#991B1B;padding:.75rem 1rem;border-radius:.5rem;margin-bottom:1.25rem;font-size:.9rem;">
            {{ $passwordError }}
        </div>
        @endif

        <form wire:submit.prevent="changePassword">
            <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:1.25rem;">
                Setting a password lets you sign in with your phone number and password instead of requesting a WhatsApp code each time.
            </p>

            @if($hasPassword ?? false)
            <div class="field">
                <label class="field__label" for="pw-current">Current Password</label>
                <input class="field__input" id="pw-current" type="password" wire:model="current_password" autocomplete="current-password">
                @error('current_password')<span class="field__error">{{ $message }}</span>@enderror
            </div>
            @endif

            <div class="field">
                <label class="field__label" for="pw-new">New Password <span style="color:var(--text-muted);font-weight:400;">min. 6 characters</span></label>
                <input class="field__input" id="pw-new" type="password" wire:model="new_password" autocomplete="new-password">
                @error('new_password')<span class="field__error">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label class="field__label" for="pw-confirm">Confirm New Password</label>
                <input class="field__input" id="pw-confirm" type="password" wire:model="new_password_confirmation" autocomplete="new-password">
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:.5rem;">
                <button type="submit" class="btn btn--primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Update Password</span>
                    <span wire:loading>Updating…</span>
                </button>
            </div>
        </form>
    </div>
</div>
