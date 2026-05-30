<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:2rem 1rem;">
<div style="width:100%;max-width:420px;">

    <div style="text-align:center;margin-bottom:2rem;">
        <div style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:600;letter-spacing:.06em;">
            STYLE<span style="color:var(--gold)">DINEE</span>
        </div>
        <div style="font-size:.85rem;color:var(--text-muted);margin-top:.5rem;">Choose your username</div>
    </div>

    <div class="card" style="padding:2rem;">

        @if($error)
        <div style="background:#FEF2F2;border:1px solid #FECACA;color:#991B1B;padding:.75rem 1rem;border-radius:8px;font-size:.85rem;margin-bottom:1.25rem;">
            {{ $error }}
        </div>
        @endif

        <p style="font-size:.88rem;color:var(--text-muted);margin-bottom:1.5rem;">
            Pick a unique username — it doubles as your referral code and cannot be changed later.
            You can also set a password to sign in without needing a WhatsApp code next time.
        </p>

        <form wire:submit="save">

            @if(!$name)
            <div class="field">
                <label class="field__label">Full Name</label>
                <input class="field__input" type="text" wire:model="name" placeholder="Amara Obi" autocomplete="name" autofocus>
                @error('name') <span class="field__error">{{ $message }}</span> @enderror
            </div>
            @endif

            <div class="field">
                <label class="field__label">Username</label>
                <input class="field__input" type="text" wire:model="username" placeholder="amara"
                    autocomplete="off" style="font-family:monospace;" {{ $name ? 'autofocus' : '' }}>
                @error('username') <span class="field__error">{{ $message }}</span> @enderror
                <span class="hint">Letters, numbers, underscores only.</span>
            </div>

            <div style="height:1px;background:var(--border);margin:1.25rem 0;"></div>

            <div class="field">
                <label class="field__label">
                    Password <span style="font-weight:400;color:var(--text-muted);">(optional)</span>
                </label>
                <input class="field__input" type="password" wire:model="password"
                    placeholder="Leave blank to always use WhatsApp code" autocomplete="new-password">
                @error('password') <span class="field__error">{{ $message }}</span> @enderror
            </div>

            @if($password)
            <div class="field">
                <label class="field__label">Confirm Password</label>
                <input class="field__input" type="password" wire:model="password_confirmation" autocomplete="new-password">
            </div>
            @endif

            <button type="submit" class="btn btn--gold" style="width:100%;justify-content:center;"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Continue to Dashboard</span>
                <span wire:loading>Saving…</span>
            </button>

        </form>
    </div>
</div>
</div>
