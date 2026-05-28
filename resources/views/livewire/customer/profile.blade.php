<div>
    <h1 class="page-title">My Profile</h1>
    <p class="page-subtitle">Update your contact details.</p>

    <div class="card">
        @if($saved)
        <div style="background:#f0faf4;border:1px solid #a8dbb8;color:#2d6a4f;padding:.75rem 1rem;border-radius:.5rem;margin-bottom:1.25rem;font-size:.9rem;">
            Profile saved successfully.
        </div>
        @endif

        {{-- Read-only: username / referral code --}}
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
                <button type="submit" class="btn btn--primary" wire:loading.attr="disabled" wire:loading.class="btn--loading">
                    <span wire:loading.remove>Save Changes</span>
                    <span wire:loading>Saving…</span>
                </button>
            </div>
        </form>
    </div>
</div>
