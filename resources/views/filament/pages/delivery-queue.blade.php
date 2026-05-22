<x-filament-panels::page>
<style>
:root {
    --bg:      #ffffff; --bg2:    #f9fafb; --bg3:    #f3f4f6;
    --border:  #e5e7eb; --border2:#d1d5db;
    --text:    #111827; --text2:  #374151; --text3:  #6b7280; --muted: #9ca3af;
    --gold:    #C9A84C; --gold-h: #b8943d;
}
.dark {
    --bg:      #1f2937; --bg2:    #111827; --bg3:    #1a2535;
    --border:  #374151; --border2:#4b5563;
    --text:    #f9fafb; --text2:  #e5e7eb; --text3:  #d1d5db; --muted: #6b7280;
}

/* ── Table ── */
.dq-wrap  { background:var(--bg); border:1px solid var(--border); border-radius:12px; overflow:hidden; }
.dq-table { width:100%; border-collapse:collapse; min-width:640px; }
.dq-table th { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); padding:.6rem 1rem; text-align:left; background:var(--bg2); border-bottom:1px solid var(--border); white-space:nowrap; }
.dq-table td { font-size:.83rem; color:var(--text2); padding:.7rem 1rem; border-bottom:1px solid var(--border); vertical-align:middle; }
.dq-table tbody tr:last-child td { border-bottom:none; }
.dq-table tbody tr:hover td { background:var(--bg2); }
.dark .dq-table tbody tr:hover td { background:rgba(255,255,255,.025); }

/* ── Cell typography ── */
.dq-ref   { font-size:.65rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); display:block; }
.dq-name  { font-weight:600; color:var(--text); font-size:.85rem; }
.dq-phone { font-size:.75rem; color:var(--text3); display:block; }
.dq-addr  { font-size:.72rem; color:var(--text3); display:block; margin-top:.15rem; }
.dq-items { font-weight:600; color:var(--text); }
.dq-amt   { display:block; font-size:.72rem; color:var(--text3); }
.badge-unpaid { font-size:.65rem; font-weight:700; color:#ef4444; }
.dark .badge-unpaid { color:#f87171; }

/* ── Status badges ── */
.dq-badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; padding:.25rem .55rem; border-radius:4px; white-space:nowrap; }
.dq-badge-awaiting  { background:rgba(245,158,11,.12); color:#92400e; border:1px solid rgba(245,158,11,.3); }
.dq-badge-assigned  { background:rgba(59,130,246,.1);  color:#1d4ed8; border:1px solid rgba(59,130,246,.25); }
.dq-badge-dispatched{ background:rgba(249,115,22,.1);  color:#c2410c; border:1px solid rgba(249,115,22,.25); }
.dark .dq-badge-awaiting   { color:#fbbf24; background:rgba(245,158,11,.1);  border-color:rgba(245,158,11,.25); }
.dark .dq-badge-assigned   { color:#93c5fd; background:rgba(59,130,246,.1);  border-color:rgba(59,130,246,.25); }
.dark .dq-badge-dispatched { color:#fb923c; background:rgba(249,115,22,.1);  border-color:rgba(249,115,22,.25); }

/* ── Action buttons ── */
.dq-actions { display:flex; gap:.35rem; align-items:center; flex-wrap:nowrap; }

.dq-btn { padding:.28rem .65rem; border-radius:5px; font-size:.74rem; font-weight:700; cursor:pointer; font-family:inherit; transition:all .15s; white-space:nowrap; }

.dq-btn-assign    { border:1px dashed var(--border2); background:transparent; color:var(--muted); }
.dq-btn-assign:hover { border-color:var(--gold); color:var(--gold); border-style:solid; }

.dq-btn-reassign  { border:1px solid var(--border2); background:transparent; color:var(--text3); }
.dq-btn-reassign:hover { border-color:var(--gold); color:var(--gold); }

.dq-btn-dispatch  { border:none; background:var(--gold); color:#111827; }
.dq-btn-dispatch:hover { background:var(--gold-h); }

.dq-btn-verify    { border:none; background:rgba(59,130,246,.12); color:#1d4ed8; }
.dq-btn-verify:hover { background:rgba(59,130,246,.22); }
.dark .dq-btn-verify      { background:rgba(59,130,246,.15); color:#93c5fd; }
.dark .dq-btn-verify:hover{ background:rgba(59,130,246,.28); }

.dq-btn-disabled  { border:1px solid rgba(245,158,11,.35); background:rgba(245,158,11,.08); color:#92400e; cursor:not-allowed; }
.dark .dq-btn-disabled { color:#fbbf24; border-color:rgba(245,158,11,.3); background:rgba(245,158,11,.07); }

/* ── Assign modal ── */
.assign-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:50; display:flex; align-items:center; justify-content:center; padding:1rem; }
.assign-box      { background:var(--bg); border:1px solid var(--border); border-radius:12px; width:100%; max-width:360px; padding:1.5rem; box-shadow:0 20px 40px rgba(0,0,0,.25); }
.assign-title    { font-size:1rem; font-weight:700; color:var(--text); margin-bottom:.2rem; }
.assign-sub      { font-size:.8rem; color:var(--text3); margin-bottom:1.1rem; }
.assign-lbl      { display:block; font-size:.65rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); margin-bottom:.35rem; }
.assign-select   { width:100%; padding:.5rem .65rem; border:1px solid var(--border2); border-radius:7px; background:var(--bg2); font-size:.85rem; color:var(--text); font-family:inherit; outline:none; margin-bottom:.9rem; }
.assign-select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.15); }
.assign-actions  { display:flex; gap:.6rem; }
.assign-btn      { flex:1; padding:.5rem; border-radius:7px; font-size:.82rem; font-weight:700; cursor:pointer; font-family:inherit; border:none; transition:all .15s; }
.assign-cancel   { background:var(--bg3); color:var(--text2); }
.dark .assign-cancel { background:var(--border); }
.assign-cancel:hover { filter:brightness(.95); }
.assign-confirm  { background:var(--gold); color:#111827; }
.assign-confirm:hover { background:var(--gold-h); }

/* ── OTP modal ── */
.otp-modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:50; display:flex; align-items:center; justify-content:center; padding:1rem; }
.otp-modal-box  { background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:2rem; width:100%; max-width:380px; box-shadow:0 20px 40px rgba(0,0,0,.25); }
.otp-title      { font-size:1.1rem; font-weight:700; color:var(--text); margin-bottom:.25rem; }
.otp-desc       { font-size:.82rem; color:var(--text3); margin-bottom:.25rem; }
.otp-input {
    width:100%; text-align:center; font-size:2rem; font-weight:700; letter-spacing:.4em;
    padding:.65rem; border:2px solid var(--border2); border-radius:8px; outline:none;
    font-family:monospace; color:var(--text); background:var(--bg2); margin:1rem 0;
    transition:border-color .15s;
}
.otp-input:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.15); }
.otp-actions    { display:flex; gap:.6rem; }
.otp-btn        { flex:1; padding:.5rem; border-radius:7px; font-size:.85rem; font-weight:700; cursor:pointer; border:none; font-family:inherit; transition:all .15s; }
.otp-cancel     { background:var(--bg3); color:var(--text2); }
.otp-cancel:hover { filter:brightness(.95); }
.otp-confirm    { background:var(--gold); color:#111827; }
.otp-confirm:hover { background:var(--gold-h); }

/* ── Payment warning (inside OTP modal) ── */
.pay-warn-box   {
    background:rgba(239,68,68,.07); border:2px solid rgba(239,68,68,.4);
    border-radius:10px; padding:1.1rem 1.25rem; margin:1rem 0;
}
.dark .pay-warn-box { background:rgba(239,68,68,.1); border-color:rgba(239,68,68,.5); }
.pay-warn-icon  { font-size:1.75rem; margin-bottom:.4rem; }
.pay-warn-title { font-size:.88rem; font-weight:800; color:#dc2626; margin-bottom:.2rem; }
.dark .pay-warn-title { color:#f87171; }
.pay-warn-body  { font-size:.8rem; color:#7f1d1d; line-height:1.55; }
.dark .pay-warn-body { color:#fca5a5; }
.pay-warn-amount { font-size:1.5rem; font-weight:900; color:#dc2626; display:block; margin:.45rem 0 .25rem; }
.dark .pay-warn-amount { color:#f87171; }
.pay-warn-status { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#b91c1c; }
.dark .pay-warn-status { color:#fca5a5; }
.otp-btn-recheck { background:rgba(239,68,68,.12); color:#dc2626; border:1.5px solid rgba(239,68,68,.35); }
.otp-btn-recheck:hover { background:rgba(239,68,68,.2); }
.dark .otp-btn-recheck { color:#f87171; border-color:rgba(239,68,68,.4); }

/* ── Cash collect sub-screen ── */
.cash-label  { display:block; font-size:.65rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); margin-bottom:.35rem; }
.cash-input  {
    width:100%; font-size:1.9rem; font-weight:700; text-align:center; letter-spacing:.05em;
    padding:.6rem; border:2px solid var(--border2); border-radius:8px; outline:none;
    font-family:monospace; color:var(--text); background:var(--bg2); margin:.35rem 0 .15rem;
    transition:border-color .15s;
}
.cash-input:focus { border-color:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,.15); }
.cash-hint   { font-size:.72rem; color:var(--muted); text-align:center; margin-bottom:.9rem; }
.otp-btn-cash { background:#10b981; color:#fff; }
.otp-btn-cash:hover { background:#059669; }
.otp-btn-back { background:transparent; border:1px solid var(--border2); color:var(--text3); }
.otp-btn-back:hover { border-color:var(--gold); color:var(--gold); }

.empty-state { text-align:center; padding:3rem; color:var(--muted); }

/* ── OTP chip (admin/cashier view only) ── */
.otp-chip {
    display:inline-flex; align-items:center; gap:.3rem; margin-top:.35rem;
    background:rgba(99,102,241,.08); border:1px solid rgba(99,102,241,.3);
    border-radius:5px; padding:.18rem .5rem;
}
.otp-chip-label { font-size:.58rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#6366f1; }
.otp-chip-code  { font-size:.82rem; font-weight:800; font-family:monospace; letter-spacing:.25em; color:#4f46e5; }
.dark .otp-chip { background:rgba(99,102,241,.12); border-color:rgba(99,102,241,.35); }
.dark .otp-chip-code, .dark .otp-chip-label { color:#818cf8; }
</style>

@php
    $orders       = $this->getOrders();
    $isCashier    = auth()->user()?->hasAnyRole(['admin', 'cashier']);
    $deliveryStaff = $isCashier ? $this->getDeliveryStaff() : [];
@endphp

@if($orders->isEmpty())
<div class="empty-state">
    <div style="font-size:2.5rem; margin-bottom:.75rem;">🚚</div>
    <p style="font-size:.95rem;">No orders ready for dispatch right now.</p>
</div>
@else
<div style="overflow-x:auto;">
<div class="dq-wrap">
<table class="dq-table">
    <thead>
        <tr>
            <th>Order</th>
            <th>Customer</th>
            <th>Items</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
    @php
        $hasDeliveryPerson = (bool) $order->delivery_user_id;
        $needsAssignment   = $order->delivery_type === 'delivery' && ! $hasDeliveryPerson;
    @endphp
    <tr wire:key="dq-{{ $order->id }}">

        <td>
            <span class="dq-ref">{{ $order->reference }}</span>
            <span style="font-size:.8rem; color:var(--text2);">{{ ucfirst($order->delivery_type) }}</span>
            @if($order->estimated_completion_date)
            <span class="dq-phone" style="margin-top:.1rem;">📅 {{ $order->estimated_completion_date->format('d M') }}</span>
            @endif
            @if($isCashier && $order->status === 'dispatched')
            @php $activeOtp = $order->latestOtp; @endphp
            @if($activeOtp && ! $activeOtp->isVerified() && ! $activeOtp->isExpired())
            <div class="otp-chip" title="Active OTP — visible to admin/cashier only">
                <span class="otp-chip-label">OTP</span>
                <span class="otp-chip-code">{{ $activeOtp->otp }}</span>
            </div>
            @endif
            @endif
        </td>

        <td>
            <span class="dq-name">{{ $order->customer_name }}</span>
            <span class="dq-phone">{{ $order->customer_phone }}</span>
            @if($order->customer_address)
            <span class="dq-addr">📍 {{ $order->customer_address }}</span>
            @endif
        </td>

        <td>
            <span class="dq-items">{{ $order->items->count() }}</span>
            <span class="dq-amt">₦{{ number_format($order->total_amount, 0) }}</span>
            @if($order->payment_status !== 'paid')
            <span class="badge-unpaid">{{ ucfirst($order->payment_status) }}</span>
            @endif
        </td>

        <td>
            @if($order->status === 'dispatched')
                <span class="dq-badge dq-badge-dispatched">🟠 Dispatched</span>
            @elseif($hasDeliveryPerson)
                <span class="dq-badge dq-badge-assigned">🔵 {{ $order->deliveryUser->name }}</span>
            @else
                <span class="dq-badge dq-badge-awaiting">🟡 Awaiting Assignment</span>
            @endif
        </td>

        <td>
            <div class="dq-actions">
                @if($order->status === 'dispatched')
                    <button wire:click="openVerify({{ $order->id }})" class="dq-btn dq-btn-verify">🔐 Verify OTP</button>

                @elseif($order->status === 'ready')

                    {{-- Assign/Reassign — cashier only --}}
                    @if($isCashier)
                        @if($hasDeliveryPerson)
                        <button wire:click="openDeliveryModal({{ $order->id }})" class="dq-btn dq-btn-reassign">↺ Reassign</button>
                        @else
                        <button wire:click="openDeliveryModal({{ $order->id }})" class="dq-btn dq-btn-assign">+ Assign Delivery</button>
                        @endif
                    @endif

                    {{-- Dispatch — blocked on unassigned delivery orders --}}
                    @if($needsAssignment)
                    <button disabled class="dq-btn dq-btn-disabled" title="Assign a delivery person first">⚠ Assign first</button>
                    @else
                    <button
                        wire:click="dispatchOrder({{ $order->id }})"
                        wire:loading.attr="disabled"
                        wire:target="dispatchOrder({{ $order->id }})"
                        class="dq-btn dq-btn-dispatch">
                        🚚 Dispatch
                    </button>
                    @endif

                @endif
            </div>
        </td>

    </tr>
    @endforeach
    </tbody>
</table>
</div>
</div>
@endif

{{-- ── Delivery Assignment Modal ── --}}
@if($showDeliveryModal)
@php $modalOrder = \App\Models\Order::find($deliveryOrderId); @endphp
<div class="assign-backdrop" wire:click.self="cancelDeliveryModal">
    <div class="assign-box">
        <div class="assign-title">Assign Delivery Person</div>
        <div class="assign-sub">{{ $modalOrder?->reference }} · {{ $modalOrder?->customer_name }}</div>

        <label class="assign-lbl">Delivery Staff</label>
        @if(! empty($deliveryStaff))
        <select wire:model.live="deliveryUserId" class="assign-select">
            <option value="">— No assignment —</option>
            @foreach($deliveryStaff as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        @else
        <select class="assign-select" disabled>
            <option>No delivery staff available</option>
        </select>
        @endif

        <div class="assign-actions">
            <button wire:click="cancelDeliveryModal" class="assign-btn assign-cancel">Cancel</button>
            <button wire:click="confirmDeliveryAssignment" class="assign-btn assign-confirm">✓ Confirm</button>
        </div>
    </div>
</div>
@endif

{{-- ── OTP Verification Modal ── --}}
@if($verifyOrderId)
@php $otpOrder = \App\Models\Order::find($verifyOrderId); @endphp
<div class="otp-modal-backdrop" wire:click.self="cancelVerify">
    <div class="otp-modal-box">

        @if($paymentWarning)
            {{-- ── Payment gate (OTP was correct but balance outstanding) ── --}}
            <div class="otp-title">🔐 OTP Verified</div>
            <p class="otp-desc">{{ $otpOrder?->reference }} · {{ $otpOrder?->customer_name }}</p>

            @if($showCashCollect)
                {{-- ── Cash collection sub-screen ── --}}
                <label class="cash-label">Amount collected from customer (₦)</label>
                <input
                    wire:model.live="cashCollectInput"
                    type="number"
                    min="0.01"
                    step="0.01"
                    max="{{ $paymentBalanceDue }}"
                    class="cash-input"
                    placeholder="{{ number_format($paymentBalanceDue, 0) }}"
                    inputmode="decimal"
                    autofocus>
                <p class="cash-hint">Balance due: ₦{{ number_format($paymentBalanceDue, 0) }}</p>
                <div class="otp-actions">
                    <button wire:click="cancelCashCollect" class="otp-btn otp-btn-back">← Back</button>
                    <button
                        wire:click="confirmCashCollect"
                        wire:loading.attr="disabled"
                        wire:target="confirmCashCollect"
                        class="otp-btn otp-btn-cash">
                        ✓ Confirm Collection
                    </button>
                </div>

            @else
                {{-- ── Warning + action choices ── --}}
                <div class="pay-warn-box">
                    <div class="pay-warn-icon">⚠️</div>
                    <div class="pay-warn-title">DO NOT HAND OVER THE CLOTHES</div>
                    <div class="pay-warn-body">
                        This order has an outstanding balance. Collect the full amount or ask the customer to pay before handing over their items.
                    </div>
                    <span class="pay-warn-amount">₦{{ number_format($paymentBalanceDue, 0) }} outstanding</span>
                    <div class="pay-warn-status">Payment status: {{ $paymentStatusLabel }}</div>
                </div>

                <div class="otp-actions" style="margin-top:.85rem;">
                    <button wire:click="cancelVerify" class="otp-btn otp-cancel">Cancel</button>
                    <button wire:click="recheckPayment"
                        wire:loading.attr="disabled"
                        wire:target="recheckPayment"
                        class="otp-btn otp-btn-recheck">
                        🔄 Paid via Transfer
                    </button>
                    <button wire:click="openCashCollect" class="otp-btn otp-btn-cash">
                        💵 Collect Cash
                    </button>
                </div>
            @endif

        @else
            {{-- ── Normal OTP entry ── --}}
            <div class="otp-title">🔐 Verify Delivery OTP</div>
            <p class="otp-desc">Ask the customer for the 6-digit OTP sent to their phone/email.</p>
            <input wire:model.live="otpInput" type="text" maxlength="6" inputmode="numeric"
                class="otp-input" placeholder="000000" autocomplete="one-time-code">
            <div class="otp-actions">
                <button wire:click="cancelVerify" class="otp-btn otp-cancel">Cancel</button>
                <button wire:click="verifyOtp"
                    wire:loading.attr="disabled"
                    wire:target="verifyOtp"
                    class="otp-btn otp-confirm">
                    ✓ Confirm Delivery
                </button>
            </div>
        @endif

    </div>
</div>
@endif

</x-filament-panels::page>
