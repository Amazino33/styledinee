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
            Pick a unique username — it doubles as your referral code. You cannot change it later.
        </p>

        <form wire:submit="save">

            @if(!$name)
            <div class="field">
                <label>Full Name</label>
                <input class="field__input" type="text" wire:model="name" placeholder="Amara Obi" autocomplete="name" autofocus>
                @error('name') <span class="field__error">{{ $message }}</span> @enderror
            </div>
            @endif

            <div class="field">
                <label>Username</label>
                <input class="field__input" type="text" wire:model="username" placeholder="amara"
                    autocomplete="off" style="font-family:monospace;" {{ $name ? 'autofocus' : '' }}>
                @error('username') <span class="field__error">{{ $message }}</span> @enderror
                <span class="hint">Letters, numbers, underscores only.</span>
            </div>

            <button type="submit" class="btn btn--gold" style="width:100%;justify-content:center;"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Confirm Username</span>
                <span wire:loading>Saving…</span>
            </button>

        </form>
    </div>
</div>
</div>
