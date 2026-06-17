<x-filament-panels::page>
<style>
:root {
    --bg:     #ffffff; --bg2:   #f9fafb; --bg3:   #f3f4f6;
    --border: #e5e7eb;
    --text:   #111827; --text2: #374151; --text3: #6b7280;
    --gold:   #C9A84C; --gold-h:#b8943d;
    --green:  #059669; --red:   #dc2626;
}
.dark {
    --bg:     #1f2937; --bg2:   #111827; --bg3:   #1a2535;
    --border: #374151;
    --text:   #f9fafb; --text2: #e5e7eb; --text3: #d1d5db;
    --gold:   #C9A84C; --gold-h:#b8943d;
    --green:  #34d399; --red:   #f87171;
}

/* ── Tab bar ── */
.s-tabs { display:flex; gap:0; border-bottom:2px solid var(--border); margin-bottom:1.5rem; }
.s-tab {
    padding:.65rem 1.25rem; font-size:.82rem; font-weight:600; letter-spacing:.04em;
    color:var(--text3); border:none; background:none; cursor:pointer;
    border-bottom:2px solid transparent; margin-bottom:-2px;
    transition:color .15s, border-color .15s; font-family:inherit;
}
.s-tab:hover { color:var(--text); }
.s-tab.active { color:var(--gold); border-bottom-color:var(--gold); }

/* ── Cards ── */
.s-card {
    background:var(--bg); border:1px solid var(--border);
    border-radius:12px; padding:24px; margin-bottom:20px;
}
.s-card-title { font-size:15px; font-weight:600; color:var(--text); margin-bottom:4px; }
.s-card-desc  { font-size:13px; color:var(--text3); margin-bottom:20px; }

/* ── Fields ── */
.s-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
@media(max-width:640px){ .s-row{ grid-template-columns:1fr; } }
.s-label {
    display:block; font-size:13px; font-weight:500; color:var(--text2); margin-bottom:6px;
}
.s-input {
    width:100%; padding:8px 12px; border:1px solid var(--border);
    border-radius:8px; background:var(--bg2); color:var(--text);
    font-size:14px; outline:none; box-sizing:border-box; transition:border-color .15s;
    font-family:inherit;
}
.s-input:focus { border-color:var(--gold); }
.s-hint { font-size:12px; color:var(--text3); margin-top:4px; }
.s-err  { font-size:12px; color:var(--red); margin-top:4px; }

/* ── Toggle ── */
.s-toggle-row {
    display:flex; align-items:center; gap:12px;
    padding:14px 0; border-top:1px solid var(--border);
    margin-bottom:20px;
}
.s-toggle-row:first-of-type { border-top:none; padding-top:0; }
.toggle-switch { position:relative; width:44px; height:24px; flex-shrink:0; cursor:pointer; }
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-track {
    position:absolute; inset:0; background:#d1d5db; border-radius:24px; transition:background .2s;
}
.toggle-switch input:checked ~ .toggle-track { background:var(--gold); }
.toggle-thumb {
    position:absolute; top:3px; left:3px; width:18px; height:18px;
    background:#fff; border-radius:50%; transition:transform .2s; box-shadow:0 1px 3px rgba(0,0,0,.2);
}
.toggle-switch input:checked ~ .toggle-track .toggle-thumb { transform:translateX(20px); }
.s-toggle-label { font-size:14px; font-weight:500; color:var(--text2); }
.s-toggle-desc  { font-size:12px; color:var(--text3); }
.status-badge {
    display:inline-flex; align-items:center; gap:6px;
    padding:4px 10px; border-radius:100px; font-size:12px; font-weight:600;
}
.status-on  { background:#dcfce7; color:#166534; }
.status-off { background:#f3f4f6; color:#6b7280; }
.dark .status-on  { background:#14532d; color:#86efac; }
.dark .status-off { background:#374151; color:#9ca3af; }

/* ── Buttons ── */
.s-btn-row { display:flex; gap:10px; justify-content:flex-end; margin-top:8px; }
.s-btn {
    padding:9px 22px; border-radius:8px; font-size:13px; font-weight:600;
    cursor:pointer; border:none; transition:background .15s; font-family:inherit;
}
.s-btn-primary   { background:var(--gold); color:#111; }
.s-btn-primary:hover { background:var(--gold-h); }
.s-btn-secondary { background:var(--bg2); color:var(--text2); border:1px solid var(--border); }
.s-btn-secondary:hover { background:var(--bg3); }
.s-btn:disabled { opacity:.5; cursor:not-allowed; }

/* ── 3-column row ── */
.s-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:16px; }
@media(max-width:640px){ .s-row-3{ grid-template-columns:1fr; } }

/* ── Payment policy option cards ── */
.s-options { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:20px; }
@media(max-width:560px){ .s-options{ grid-template-columns:1fr; } }
.s-option {
    border:2px solid var(--border); border-radius:10px; padding:16px;
    cursor:pointer; transition:border-color .15s, background .15s; position:relative;
}
.s-option:hover { border-color:var(--gold); }
.s-option.selected { border-color:var(--gold); background:rgba(201,168,76,.07); }
.s-option input { position:absolute; opacity:0; width:0; height:0; }
.s-option-title { font-size:14px; font-weight:600; color:var(--text); margin-bottom:4px; display:flex; align-items:center; gap:8px; }
.s-option-desc  { font-size:12px; color:var(--text3); line-height:1.5; }
.s-check {
    width:18px; height:18px; border-radius:50%; border:2px solid var(--border);
    flex-shrink:0; display:flex; align-items:center; justify-content:center; transition:all .15s;
}
.s-option.selected .s-check { border-color:var(--gold); background:var(--gold); }
.s-check::after { content:'✓'; font-size:10px; color:#fff; display:none; }
.s-option.selected .s-check::after { display:block; }
</style>

{{-- ── Tab bar ── --}}
<div class="s-tabs">
    <button class="s-tab {{ $tab === 'payment'   ? 'active' : '' }}" wire:click="$set('tab','payment')">
        💳 Payment
    </button>
    <button class="s-tab {{ $tab === 'messaging' ? 'active' : '' }}" wire:click="$set('tab','messaging')">
        💬 Messaging
    </button>
    <button class="s-tab {{ $tab === 'referral'  ? 'active' : '' }}" wire:click="$set('tab','referral')">
        🤝 Referral & Affiliate
    </button>
    <button class="s-tab {{ $tab === 'pos' ? 'active' : '' }}" wire:click="$set('tab','pos')">
        🧵 POS
    </button>
</div>

{{-- ══════════ PAYMENT TAB ══════════ --}}
@if($tab === 'payment')

<div class="s-card">
    <div class="s-card-title">Payment Policy</div>
    <div class="s-card-desc">Choose when customers are required to pay for their orders at the POS.</div>

    <div class="s-options">
        <label class="s-option {{ $payment_policy === 'half_upfront' ? 'selected' : '' }}">
            <input type="radio" wire:model.live="payment_policy" value="half_upfront">
            <div class="s-option-title">
                <span class="s-check"></span>
                💳 Pay Deposit Upfront
            </div>
            <div class="s-option-desc">
                Customers must pay a minimum deposit when placing the order.
                The remaining balance is collected on pickup or delivery.
            </div>
        </label>
        <label class="s-option {{ $payment_policy === 'pay_later' ? 'selected' : '' }}">
            <input type="radio" wire:model.live="payment_policy" value="pay_later">
            <div class="s-option-title">
                <span class="s-check"></span>
                🕐 Pay After Production
            </div>
            <div class="s-option-desc">
                No upfront payment required. Customers pay in full when the order
                is ready for pickup or delivered.
            </div>
        </label>
    </div>

    @if($payment_policy === 'half_upfront')
    <div style="border-top:1px solid var(--border);padding-top:18px;margin-bottom:20px;">
        <label class="s-label">Minimum Deposit Required</label>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
            <input type="number" wire:model="deposit_percent" min="1" max="100"
                   class="s-input" style="width:80px;text-align:center;font-size:16px;font-weight:700;">
            <span style="font-size:18px;font-weight:700;color:var(--text2);">%</span>
            <span style="font-size:13px;color:var(--text3);">of the total order amount</span>
        </div>
        <div class="s-hint">E.g. for a ₦20,000 order, {{ $deposit_percent }}% = ₦{{ number_format(20000 * $deposit_percent / 100, 0) }}</div>
        @error('deposit_percent')<p class="s-err">{{ $message }}</p>@enderror
    </div>
    @endif

    <div class="s-card" style="background:rgba(201,168,76,.06);border-color:rgba(201,168,76,.3);margin-bottom:0;">
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:1.2rem;">ℹ️</span>
            <div style="font-size:13px;color:var(--text3);">
                @if($payment_policy === 'half_upfront')
                    Cashiers must collect at least <strong>{{ $deposit_percent }}%</strong> before confirming an order at the POS.
                @else
                    Cashiers can confirm orders without collecting any payment. Full payment is due on completion.
                @endif
            </div>
        </div>
    </div>
</div>

<div class="s-btn-row">
    <button class="s-btn s-btn-primary" wire:click="savePayment" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="savePayment">Save Payment Settings</span>
        <span wire:loading wire:target="savePayment">Saving…</span>
    </button>
</div>

@endif

{{-- ══════════ MESSAGING TAB ══════════ --}}
@if($tab === 'messaging')

{{-- WhatsApp credentials --}}
<div class="s-card">
    <div class="s-card-title">WAWP — WhatsApp</div>
    <div class="s-card-desc">
        Your WAWP instance ID and access token from
        <a href="https://app.wawp.net" target="_blank" style="color:var(--gold)">app.wawp.net</a>.
    </div>

    <div class="s-row">
        <div>
            <label class="s-label">Instance ID</label>
            <input type="text" wire:model="instance_id" class="s-input" placeholder="e.g. 0F8B7C26C87E" autocomplete="off">
            @error('instance_id')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="s-label">Access Token</label>
            <input type="password" wire:model="access_token" class="s-input" placeholder="Paste your access token" autocomplete="new-password">
            @error('access_token')<p class="s-err">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="s-toggle-row">
        <label class="toggle-switch">
            <input type="checkbox" wire:model="enabled">
            <span class="toggle-track"><span class="toggle-thumb"></span></span>
        </label>
        <div>
            <div class="s-toggle-label">Enable WhatsApp Notifications</div>
            <div class="s-toggle-desc">When off, messages are logged locally instead of being sent.</div>
        </div>
        <div style="margin-left:auto">
            <span class="status-badge {{ $enabled ? 'status-on' : 'status-off' }}">
                <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block"></span>
                {{ $enabled ? 'Enabled' : 'Disabled' }}
            </span>
        </div>
    </div>
</div>

{{-- SMS Fallback --}}
<div class="s-card">
    <div class="s-card-title">SMS Fallback</div>
    <div class="s-card-desc">When WhatsApp delivery fails, messages are retried via SMS.</div>

    <div class="s-toggle-row">
        <label class="toggle-switch">
            <input type="checkbox" wire:model="sms_enabled">
            <span class="toggle-track"><span class="toggle-thumb"></span></span>
        </label>
        <div>
            <div class="s-toggle-label">Enable SMS Fallback</div>
            <div class="s-toggle-desc">Sends SMS when WhatsApp cannot deliver (e.g. number not on WhatsApp).</div>
        </div>
        <div style="margin-left:auto">
            <span class="status-badge {{ $sms_enabled ? 'status-on' : 'status-off' }}">
                <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block"></span>
                {{ $sms_enabled ? 'Enabled' : 'Disabled' }}
            </span>
        </div>
    </div>

    <div class="s-row">
        <div>
            <label class="s-label">SMS Provider</label>
            <select wire:model="sms_provider" class="s-input" style="cursor:pointer;">
                <option value="termii">Termii</option>
                <option value="bulksms">BulkSMS Nigeria</option>
                <option value="kudisms">KudiSMS</option>
            </select>
        </div>
        <div>
            <label class="s-label">Sender ID / Name</label>
            <input type="text" wire:model="sms_sender_id" class="s-input" placeholder="Styledinee" maxlength="11">
            @error('sms_sender_id')<p class="s-err">{{ $message }}</p>@enderror
        </div>
    </div>
    <div class="s-row">
        <div>
            <label class="s-label">API Key</label>
            <input type="password" wire:model="sms_api_key" class="s-input" placeholder="Paste API key" autocomplete="new-password">
            @error('sms_api_key')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="s-label">API Secret <span style="font-size:11px;color:var(--text3)">(BulkSMS only)</span></label>
            <input type="password" wire:model="sms_api_secret" class="s-input" placeholder="Only required for BulkSMS" autocomplete="new-password">
            @error('sms_api_secret')<p class="s-err">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

{{-- OTP Rate Limiting --}}
<div class="s-card">
    <div class="s-card-title">OTP Rate Limiting</div>
    <div class="s-card-desc">Prevent spam by limiting how many login codes can be sent per number within a time window.</div>

    <div class="s-row">
        <div>
            <label class="s-label">Time Window (minutes)</label>
            <input type="number" min="1" max="60" wire:model="otp_window_minutes" class="s-input">
            @error('otp_window_minutes')<p class="s-err">{{ $message }}</p>@enderror
            <p class="s-hint">How long the window is tracked. Default: 10 min.</p>
        </div>
        <div>
            <label class="s-label">Max Requests per Window</label>
            <input type="number" min="1" max="10" wire:model="otp_max_attempts" class="s-input">
            @error('otp_max_attempts')<p class="s-err">{{ $message }}</p>@enderror
            <p class="s-hint">Block requests beyond this count. Default: 3.</p>
        </div>
    </div>
</div>

{{-- Broadcast Delay --}}
<div class="s-card">
    <div class="s-card-title">Broadcast Message Delay</div>
    <div class="s-card-desc">Controls how fast mass messages are dispatched. Higher = faster but risks provider throttling.</div>

    <div class="s-row" style="max-width:400px;">
        <div>
            <label class="s-label">Delay Between Messages (seconds)</label>
            <input type="number" min="1" max="60" wire:model="broadcast_delay_seconds" class="s-input">
            @error('broadcast_delay_seconds')<p class="s-err">{{ $message }}</p>@enderror
            <p class="s-hint">Seconds between each message. Default: 3s. Min: 1s, Max: 60s.</p>
        </div>
    </div>
</div>

{{-- Save --}}
<div class="s-btn-row" style="margin-bottom:20px;">
    <button class="s-btn s-btn-primary" wire:click="saveMessaging" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="saveMessaging">Save Messaging Settings</span>
        <span wire:loading wire:target="saveMessaging">Saving…</span>
    </button>
</div>

{{-- Test message --}}
<div class="s-card">
    <div class="s-card-title">Send Test Message</div>
    <div class="s-card-desc">
        Verify your configuration by sending a test WhatsApp/SMS to any number (include country code, e.g.
        <code style="background:var(--bg3);padding:1px 5px;border-radius:4px;font-size:12px;">2348012345678</code>).
    </div>
    <div style="display:flex;gap:12px;align-items:flex-start;">
        <div style="flex:1;">
            <input type="text" wire:model="test_number" class="s-input" placeholder="2348012345678" style="max-width:320px;">
            @error('test_number')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <button class="s-btn s-btn-secondary" wire:click="sendTest" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="sendTest">Send Test</span>
            <span wire:loading wire:target="sendTest">Sending…</span>
        </button>
    </div>
</div>

{{-- What gets sent --}}
<div class="s-card" style="background:var(--bg2);">
    <div class="s-card-title" style="margin-bottom:10px;">What gets sent via WhatsApp?</div>
    <ul style="margin:0;padding-left:20px;color:var(--text2);font-size:13px;line-height:1.9;">
        <li>Order confirmation (customer)</li>
        <li>Production stage updates — Sewing, Embroidery, Printing, Finishing (customer)</li>
        <li>Order ready for pickup / delivery (customer)</li>
        <li>Delivery OTP (customer)</li>
        <li>Staff assignment notifications (staff member)</li>
        <li>90% production time deadline alerts (staff + cashier + admins)</li>
        <li>Customer login OTP</li>
    </ul>
</div>

@endif

{{-- ══════════ REFERRAL & AFFILIATE TAB ══════════ --}}
@if($tab === 'referral')

{{-- Referral Program --}}
<div class="s-card">
    <div class="s-card-title">Referral Program (One-time Reward)</div>
    <div class="s-card-desc">
        When someone uses a referral code at signup and places their first order,
        the referrer earns a one-time reward.
    </div>

    <div class="s-toggle-row" style="border-top:none;padding-top:0;">
        <label class="toggle-switch">
            <input type="checkbox" wire:model="referral_enabled">
            <span class="toggle-track"><span class="toggle-thumb"></span></span>
        </label>
        <div>
            <div class="s-toggle-label">Enable Referral Program</div>
        </div>
    </div>

    <div class="s-row-3">
        <div>
            <label class="s-label">Default Reward Amount (₦)</label>
            <input type="number" min="0" step="100" wire:model="referral_default_amount" class="s-input" placeholder="e.g. 2000">
            @error('referral_default_amount')<p class="s-err">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="s-label">Minimum Order Amount (₦)</label>
            <input type="number" min="0" step="100" wire:model="referral_min_order_amount" class="s-input" placeholder="0 = no minimum">
            <p class="s-hint">Order must be at least this amount to trigger reward.</p>
        </div>
        <div>
            <label class="s-label">Default Payout Method</label>
            <select wire:model="referral_default_payout" class="s-input" style="cursor:pointer;">
                <option value="credit">Account Credit</option>
                <option value="bank_transfer">Bank Transfer</option>
            </select>
            @error('referral_default_payout')<p class="s-err">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="s-toggle-row">
        <label class="toggle-switch">
            <input type="checkbox" wire:model="referral_auto_trigger">
            <span class="toggle-track"><span class="toggle-thumb"></span></span>
        </label>
        <div>
            <div class="s-toggle-label">Auto-trigger Payout</div>
            <div class="s-toggle-desc">
                When enabled, the referral reward is processed automatically when the first order completes.
                When disabled, admin manually approves each conversion.
            </div>
        </div>
    </div>
</div>

{{-- Affiliate Program --}}
<div class="s-card">
    <div class="s-card-title">Affiliate Program (Recurring Commission)</div>
    <div class="s-card-desc">
        Approved affiliates earn a percentage of every future order placed by the customers they referred.
    </div>

    <div class="s-toggle-row" style="border-top:none;padding-top:0;">
        <label class="toggle-switch">
            <input type="checkbox" wire:model="affiliate_enabled">
            <span class="toggle-track"><span class="toggle-thumb"></span></span>
        </label>
        <div>
            <div class="s-toggle-label">Enable Affiliate Program</div>
        </div>
    </div>

    <div class="s-toggle-row">
        <label class="toggle-switch">
            <input type="checkbox" wire:model="affiliate_registration_open">
            <span class="toggle-track"><span class="toggle-thumb"></span></span>
        </label>
        <div>
            <div class="s-toggle-label">Open Public Registration</div>
            <div class="s-toggle-desc">
                When enabled, anyone can submit an affiliate application via the public registration page.
                Disable to allow admin-only enrollment.
            </div>
        </div>
    </div>

    <div class="s-row-3" style="margin-top:8px;">
        <div>
            <label class="s-label">Default Commission Rate (%)</label>
            <input type="number" min="0" max="100" step="0.5" wire:model="affiliate_default_rate" class="s-input" placeholder="e.g. 5">
            @error('affiliate_default_rate')<p class="s-err">{{ $message }}</p>@enderror
            <p class="s-hint">Individual affiliates can override this rate.</p>
        </div>
        <div>
            <label class="s-label">Minimum Order Amount (₦)</label>
            <input type="number" min="0" step="100" wire:model="affiliate_min_order_amount" class="s-input" placeholder="0 = no minimum">
            <p class="s-hint">Order must reach this amount to earn commission.</p>
        </div>
        <div>
            <label class="s-label">Default Payout Method</label>
            <select wire:model="affiliate_default_payout" class="s-input" style="cursor:pointer;">
                <option value="bank_transfer">Bank Transfer</option>
                <option value="credit">Account Credit</option>
            </select>
            @error('affiliate_default_payout')<p class="s-err">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="s-toggle-row">
        <label class="toggle-switch">
            <input type="checkbox" wire:model="affiliate_auto_approve">
            <span class="toggle-track"><span class="toggle-thumb"></span></span>
        </label>
        <div>
            <div class="s-toggle-label">Auto-approve Commissions</div>
            <div class="s-toggle-desc">
                When enabled, commissions are approved automatically when an order is paid.
                When disabled, admin reviews and approves each commission manually.
            </div>
        </div>
    </div>
</div>

{{-- Credit Wallet --}}
<div class="s-card">
    <div class="s-card-title">Referral Credit Wallet</div>
    <div class="s-card-desc">
        Controls how account credits (from referral rewards and commissions) are applied to orders.
    </div>

    <div class="s-toggle-row" style="border-top:none;padding-top:0;">
        <label class="toggle-switch">
            <input type="checkbox" wire:model="credit_auto_apply">
            <span class="toggle-track"><span class="toggle-thumb"></span></span>
        </label>
        <div>
            <div class="s-toggle-label">Auto-apply Credit on Next Order</div>
            <div class="s-toggle-desc">
                When enabled, available credit is automatically applied at checkout.
                Customers can still remove it and choose a specific order manually.
            </div>
        </div>
    </div>
</div>

<div class="s-btn-row">
    <button class="s-btn s-btn-primary" wire:click="saveReferral" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="saveReferral">Save Referral & Affiliate Settings</span>
        <span wire:loading wire:target="saveReferral">Saving…</span>
    </button>
</div>

@endif

{{-- ══════════ POS TAB ══════════ --}}
@if($tab === 'pos')

<div class="s-card">
    <div class="s-card-title">Bill of Materials (BOM)</div>
    <div class="s-card-desc">
        Control how BOM is displayed and managed during the POS order flow.
        This applies to both the product-adding modal and the cart sidebar.
    </div>

    <div class="s-options" style="grid-template-columns:repeat(2,1fr);">
        <label class="s-option {{ $bom_mode === 'full' ? 'selected' : '' }}">
            <input type="radio" wire:model.live="bom_mode" value="full">
            <div class="s-option-title">
                <span class="s-check"></span>
                🧵 Full BOM
            </div>
            <div class="s-option-desc">
                Cashiers see all materials, can add new lines, adjust quantities, and record removals with reasons.
            </div>
        </label>
        <label class="s-option {{ $bom_mode === 'remove_only' ? 'selected' : '' }}">
            <input type="radio" wire:model.live="bom_mode" value="remove_only">
            <div class="s-option-title">
                <span class="s-check"></span>
                ✂️ Remove Only
            </div>
            <div class="s-option-desc">
                Cashiers can remove materials (with reason) but cannot add new lines to the BOM.
            </div>
        </label>
        <label class="s-option {{ $bom_mode === 'view_only' ? 'selected' : '' }}">
            <input type="radio" wire:model.live="bom_mode" value="view_only">
            <div class="s-option-title">
                <span class="s-check"></span>
                👁 View Only
            </div>
            <div class="s-option-desc">
                BOM is shown for reference but cashiers cannot add or remove materials.
            </div>
        </label>
        <label class="s-option {{ $bom_mode === 'disabled' ? 'selected' : '' }}">
            <input type="radio" wire:model.live="bom_mode" value="disabled">
            <div class="s-option-title">
                <span class="s-check"></span>
                🚫 Hidden
            </div>
            <div class="s-option-desc">
                BOM is completely hidden from the POS. Cashiers only see items and prices.
            </div>
        </label>
    </div>
</div>

<div class="s-btn-row">
    <button class="s-btn s-btn-primary" wire:click="savePos" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="savePos">Save POS Settings</span>
        <span wire:loading wire:target="savePos">Saving…</span>
    </button>
</div>

@endif

</x-filament-panels::page>
