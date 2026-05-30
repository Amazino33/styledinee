<x-filament-panels::page>
<style>
:root {
    --bg: #ffffff; --bg2: #f9fafb; --bg3: #f3f4f6;
    --border: #e5e7eb; --text: #111827; --text2: #374151; --text3: #6b7280;
    --gold: #C9A84C; --gold-h: #b8943d; --green: #059669; --red: #dc2626;
}
.dark {
    --bg: #1f2937; --bg2: #111827; --bg3: #1a2535;
    --border: #374151; --text: #f9fafb; --text2: #e5e7eb; --text3: #d1d5db;
    --gold: #C9A84C; --gold-h: #b8943d; --green: #34d399; --red: #f87171;
}
.card { background: var(--bg); border: 1px solid var(--border); border-radius:12px; padding:24px; margin-bottom:20px; }
.card-title { font-size:15px; font-weight:600; color:var(--text); margin-bottom:4px; }
.card-desc  { font-size:13px; color:var(--text3); margin-bottom:20px; }
.grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
.grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:16px; }
@media(max-width:640px) { .grid-2,.grid-3 { grid-template-columns:1fr; } }
.field-label { display:block; font-size:13px; font-weight:500; color:var(--text2); margin-bottom:6px; }
.field-hint  { font-size:11px; color:var(--text3); margin-top:4px; }
.field-input {
    width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:8px;
    background:var(--bg2); color:var(--text); font-size:14px; outline:none; box-sizing:border-box; transition:border-color .15s;
}
.field-input:focus { border-color:var(--gold); }
.field-select { width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:8px; background:var(--bg2); color:var(--text); font-size:14px; outline:none; box-sizing:border-box; }
.toggle-row { display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid var(--border); margin-bottom:12px; }
.toggle-row:last-child { border-bottom:none; margin-bottom:0; }
.toggle-label { font-size:14px; font-weight:500; color:var(--text2); }
.toggle-desc  { font-size:12px; color:var(--text3); }
.toggle-switch { position:relative; width:44px; height:24px; flex-shrink:0; cursor:pointer; }
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-track { position:absolute; inset:0; background:#d1d5db; border-radius:24px; transition:background .2s; }
.toggle-switch input:checked ~ .toggle-track { background:var(--gold); }
.toggle-thumb { position:absolute; top:3px; left:3px; width:18px; height:18px; background:#fff; border-radius:50%; transition:transform .2s; box-shadow:0 1px 3px rgba(0,0,0,.2); }
.toggle-switch input:checked ~ .toggle-track .toggle-thumb { transform:translateX(20px); }
.toggle-info  { flex:1; }
.btn { padding:9px 20px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:background .15s; }
.btn-primary { background:var(--gold); color:#fff; }
.btn-primary:hover { background:var(--gold-h); }
.btn-row { display:flex; justify-content:flex-end; margin-top:20px; }
.section-divider { height:1px; background:var(--border); margin:20px 0; }
</style>

<div>

    {{-- ── Referral Program ── --}}
    <div class="card">
        <div class="card-title">Referral Program (One-time Reward)</div>
        <div class="card-desc">
            When someone uses a referral code at signup and places their first order,
            the referrer earns a one-time reward.
        </div>

        <div class="toggle-row">
            <label class="toggle-switch">
                <input type="checkbox" wire:model="referral_enabled">
                <span class="toggle-track"><span class="toggle-thumb"></span></span>
            </label>
            <div class="toggle-info">
                <div class="toggle-label">Enable Referral Program</div>
            </div>
        </div>

        <div class="grid-3">
            <div>
                <label class="field-label">Default Reward Amount (₦)</label>
                <input type="number" min="0" step="100" wire:model="referral_default_amount" class="field-input" placeholder="e.g. 2000">
                @error('referral_default_amount') <p style="color:var(--red);font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="field-label">Minimum Order Amount (₦)</label>
                <input type="number" min="0" step="100" wire:model="referral_min_order_amount" class="field-input" placeholder="0 = no minimum">
                <p class="field-hint">Order must be at least this amount to trigger reward.</p>
            </div>
            <div>
                <label class="field-label">Default Payout Method</label>
                <select wire:model="referral_default_payout" class="field-select">
                    <option value="credit">Account Credit</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
                <p class="field-hint">Individual affiliates can have their own override.</p>
            </div>
        </div>

        <div class="section-divider"></div>

        <div class="toggle-row">
            <label class="toggle-switch">
                <input type="checkbox" wire:model="referral_auto_trigger">
                <span class="toggle-track"><span class="toggle-thumb"></span></span>
            </label>
            <div class="toggle-info">
                <div class="toggle-label">Auto-trigger Payout</div>
                <div class="toggle-desc">
                    When enabled, the referral reward is processed automatically when the first order completes.
                    When disabled, admin manually approves each conversion.
                </div>
            </div>
        </div>
    </div>

    {{-- ── Affiliate Program ── --}}
    <div class="card">
        <div class="card-title">Affiliate Program (Recurring Commission)</div>
        <div class="card-desc">
            Approved affiliates earn a percentage of every future order placed by the customers they referred.
        </div>

        <div class="toggle-row">
            <label class="toggle-switch">
                <input type="checkbox" wire:model="affiliate_enabled">
                <span class="toggle-track"><span class="toggle-thumb"></span></span>
            </label>
            <div class="toggle-info">
                <div class="toggle-label">Enable Affiliate Program</div>
            </div>
        </div>

        <div class="toggle-row">
            <label class="toggle-switch">
                <input type="checkbox" wire:model="affiliate_registration_open">
                <span class="toggle-track"><span class="toggle-thumb"></span></span>
            </label>
            <div class="toggle-info">
                <div class="toggle-label">Open Public Registration</div>
                <div class="toggle-desc">
                    When enabled, anyone can submit an affiliate application via the public registration page.
                    Disable to allow admin-only enrollment.
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <div class="grid-3">
            <div>
                <label class="field-label">Default Commission Rate (%)</label>
                <input type="number" min="0" max="100" step="0.5" wire:model="affiliate_default_rate" class="field-input" placeholder="e.g. 5">
                @error('affiliate_default_rate') <p style="color:var(--red);font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
                <p class="field-hint">Individual affiliates can have their own override rate.</p>
            </div>
            <div>
                <label class="field-label">Minimum Order Amount (₦)</label>
                <input type="number" min="0" step="100" wire:model="affiliate_min_order_amount" class="field-input" placeholder="0 = no minimum">
                <p class="field-hint">Order must reach this amount to earn commission.</p>
            </div>
            <div>
                <label class="field-label">Default Payout Method</label>
                <select wire:model="affiliate_default_payout" class="field-select">
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="credit">Account Credit</option>
                </select>
            </div>
        </div>

        <div class="section-divider"></div>

        <div class="toggle-row">
            <label class="toggle-switch">
                <input type="checkbox" wire:model="affiliate_auto_approve">
                <span class="toggle-track"><span class="toggle-thumb"></span></span>
            </label>
            <div class="toggle-info">
                <div class="toggle-label">Auto-approve Commissions</div>
                <div class="toggle-desc">
                    When enabled, commissions are approved automatically when an order is paid.
                    When disabled, admin reviews and approves each commission manually.
                </div>
            </div>
        </div>
    </div>

    {{-- ── Credit Wallet ── --}}
    <div class="card">
        <div class="card-title">Referral Credit Wallet</div>
        <div class="card-desc">
            Controls how account credits (from referral rewards and commissions) are applied to orders.
        </div>

        <div class="toggle-row">
            <label class="toggle-switch">
                <input type="checkbox" wire:model="credit_auto_apply">
                <span class="toggle-track"><span class="toggle-thumb"></span></span>
            </label>
            <div class="toggle-info">
                <div class="toggle-label">Auto-apply Credit on Next Order</div>
                <div class="toggle-desc">
                    When enabled, available credit is automatically applied at checkout.
                    Customers can still remove it and choose a specific order manually.
                </div>
            </div>
        </div>
    </div>

    <div class="btn-row">
        <button class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="save">Save Settings</span>
            <span wire:loading wire:target="save">Saving…</span>
        </button>
    </div>

</div>
</x-filament-panels::page>
