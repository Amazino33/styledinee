<x-filament-panels::page>
<style>
:root {
    --bg:     #ffffff; --bg2: #f9fafb; --bg3: #f3f4f6;
    --border: #e5e7eb; --border2: #d1d5db;
    --text:   #111827; --text2: #374151; --text3: #6b7280; --muted: #9ca3af;
    --gold:   #C9A84C; --gold-h: #b8943d; --gold-light: rgba(201,168,76,.10);
    --radius: 10px;
}
.dark {
    --bg:     #1f2937; --bg2: #111827; --bg3: #1a2535;
    --border: #374151; --border2: #4b5563;
    --text:   #f9fafb; --text2: #e5e7eb; --text3: #d1d5db; --muted: #6b7280;
}

/* ── Toolbar ── */
.eod-toolbar {
    display:flex; flex-wrap:wrap; align-items:center; gap:.75rem;
    background:var(--bg); border:1px solid var(--border); border-radius:var(--radius);
    padding:.75rem 1rem; margin-bottom:1.25rem;
}
.eod-toolbar-title { font-size:.85rem; font-weight:700; color:var(--text); flex:1; }
.eod-date-input {
    padding:.4rem .7rem; border:1px solid var(--border2); border-radius:7px;
    background:var(--bg2); color:var(--text); font-size:.83rem; outline:none;
    font-family:inherit; cursor:pointer;
}
.eod-date-input:focus { border-color:var(--gold); }

/* ── Summary cards ── */
.eod-cards { display:grid; grid-template-columns:repeat(5,1fr); gap:.85rem; margin-bottom:1.25rem; }
@media(max-width:900px){ .eod-cards { grid-template-columns:repeat(3,1fr); } }
@media(max-width:540px){ .eod-cards { grid-template-columns:repeat(2,1fr); } }
.eod-card {
    background:var(--bg); border:1px solid var(--border); border-radius:var(--radius);
    padding:.9rem 1rem;
}
.eod-card-label { font-size:.6rem; font-weight:700; text-transform:uppercase; letter-spacing:.12em; color:var(--muted); margin-bottom:.3rem; }
.eod-card-val   { font-size:1.35rem; font-weight:800; color:var(--gold); line-height:1.1; }
.eod-card-sub   { font-size:.7rem; color:var(--text3); margin-top:.2rem; }
.eod-card.total { border-color:var(--gold); background:var(--gold-light); }
.eod-card.total .eod-card-val { font-size:1.55rem; }

/* ── Two-column layout ── */
.eod-grid { display:grid; grid-template-columns:1fr 340px; gap:1.25rem; align-items:start; }
@media(max-width:860px){ .eod-grid { grid-template-columns:1fr; } }

/* ── Shared panel ── */
.eod-panel {
    background:var(--bg); border:1px solid var(--border); border-radius:var(--radius);
    overflow:hidden; margin-bottom:1.25rem;
}
.eod-panel-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:.65rem 1rem; background:var(--bg2); border-bottom:1px solid var(--border);
}
.eod-panel-title { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--text3); }
.eod-panel-badge {
    font-size:.65rem; font-weight:700; padding:.15rem .5rem; border-radius:999px;
    background:rgba(239,68,68,.12); color:#dc2626;
}
.eod-panel-badge.ok { background:rgba(34,197,94,.12); color:#16a34a; }
.eod-panel-body { padding:.85rem 1rem; }

/* ── Outstanding orders ── */
.eod-order-row {
    display:grid; grid-template-columns:auto 1fr auto auto; gap:.5rem .75rem;
    align-items:center; padding:.5rem 0; border-bottom:1px solid var(--border);
    font-size:.81rem;
}
.eod-order-row:last-child { border-bottom:none; }
.eod-order-ref { font-weight:700; color:var(--text); font-size:.75rem; }
.eod-order-name { color:var(--text2); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.eod-order-total { font-weight:600; color:var(--text); white-space:nowrap; text-align:right; }
.eod-order-status {
    font-size:.62rem; font-weight:700; text-transform:uppercase; padding:.15rem .45rem;
    border-radius:999px; white-space:nowrap;
}
.eod-order-status.unpaid { background:rgba(239,68,68,.12); color:#dc2626; }
.eod-order-status.partial { background:rgba(234,179,8,.12); color:#b45309; }
.eod-empty { text-align:center; color:var(--muted); padding:1.5rem 0; font-size:.83rem; }

/* ── Cash reconciliation panel ── */
.eod-recon-row { display:flex; justify-content:space-between; align-items:baseline; padding:.35rem 0; font-size:.84rem; }
.eod-recon-label { color:var(--text3); }
.eod-recon-val { font-weight:700; color:var(--text); }
.eod-recon-divider { height:1px; background:var(--border); margin:.5rem 0; }
.eod-recon-disc { font-size:1rem; font-weight:800; }
.eod-recon-disc.balanced { color:#16a34a; }
.eod-recon-disc.shortage  { color:#dc2626; }
.eod-recon-disc.overage   { color:#b45309; }

.eod-field { display:flex; flex-direction:column; gap:.3rem; margin:.75rem 0; }
.eod-field label { font-size:.75rem; font-weight:600; color:var(--text2); }
.eod-input {
    padding:.5rem .75rem; border:1px solid var(--border2); border-radius:7px;
    background:var(--bg2); color:var(--text); font-size:.88rem; outline:none;
    font-family:inherit; width:100%; box-sizing:border-box; transition:border-color .15s;
}
.eod-input:focus { border-color:var(--gold); }
.eod-textarea { resize:vertical; min-height:60px; }
.eod-err { font-size:.72rem; color:#dc2626; margin:0; }

.eod-close-btn {
    width:100%; padding:.6rem; background:var(--gold); color:#111; border:none;
    border-radius:8px; font-size:.88rem; font-weight:700; cursor:pointer;
    font-family:inherit; margin-top:.75rem; transition:background .15s;
}
.eod-close-btn:hover { background:var(--gold-h); }
.eod-close-btn:disabled { opacity:.5; cursor:not-allowed; }

/* ── Saved banner ── */
.eod-saved-banner {
    display:flex; align-items:center; gap:.6rem; padding:.65rem .9rem;
    border-radius:8px; font-size:.8rem; margin-bottom:.75rem;
}
.eod-saved-banner.balanced { background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.3); color:#15803d; }
.eod-saved-banner.shortage  { background:rgba(239,68,68,.08); border:1px solid rgba(239,68,68,.3); color:#b91c1c; }
.eod-saved-banner.overage   { background:rgba(234,179,8,.08); border:1px solid rgba(234,179,8,.3); color:#92400e; }
.dark .eod-saved-banner.balanced { color:#4ade80; }
.dark .eod-saved-banner.shortage  { color:#f87171; }
.dark .eod-saved-banner.overage   { color:#fcd34d; }

/* ── History table ── */
.eod-hist-wrap { background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; margin-top:1.5rem; }
.eod-hist-head { padding:.65rem 1rem; background:var(--bg2); border-bottom:1px solid var(--border); }
.eod-hist-head-title { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--text3); }
.eod-hist-table { width:100%; border-collapse:collapse; font-size:.81rem; }
.eod-hist-table th {
    text-align:left; padding:.55rem 1rem; font-size:.63rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.1em; color:var(--muted);
    border-bottom:1px solid var(--border); background:var(--bg2); white-space:nowrap;
}
.eod-hist-table td { padding:.6rem 1rem; border-bottom:1px solid var(--border); vertical-align:middle; }
.eod-hist-table tr:last-child td { border-bottom:none; }
.eod-hist-table tr:hover td { background:var(--bg2); }
.eod-hist-date { font-weight:700; color:var(--text); white-space:nowrap; }
.eod-hist-by   { font-size:.75rem; color:var(--text3); }
.eod-hist-amt  { font-weight:600; color:var(--text); }
.eod-hist-disc { font-weight:700; font-size:.8rem; }
.eod-hist-disc.balanced { color:#16a34a; }
.eod-hist-disc.shortage  { color:#dc2626; }
.eod-hist-disc.overage   { color:#b45309; }
.dark .eod-hist-disc.balanced { color:#4ade80; }
.dark .eod-hist-disc.shortage  { color:#f87171; }
.dark .eod-hist-disc.overage   { color:#fcd34d; }
.eod-hist-empty { text-align:center; color:var(--muted); padding:2.5rem; font-size:.83rem; }
</style>

@php
    $summary       = $this->getPaymentSummary();
    $outstanding   = $this->getOutstandingOrders();
    $driverPending = $this->getPendingDriverCashCount();
    $existing      = $this->getExistingReconciliation();
    $history       = $this->getRecentReconciliations();
    $isAdmin       = auth()->user()?->hasRole('admin');

    $cashExpected  = $summary['cash'];
    $cashCounted   = is_numeric($cashCounted) ? (float) $cashCounted : null;
    $discrepancy   = $cashCounted !== null ? round($cashCounted - $cashExpected, 2) : null;
    $discStatus    = $discrepancy === null ? 'neutral'
        : ($discrepancy === 0.0 ? 'balanced' : ($discrepancy > 0 ? 'overage' : 'shortage'));
@endphp

{{-- Toolbar --}}
<div class="eod-toolbar">
    <span class="eod-toolbar-title">
        {{ now()->toDateString() === $selectedDate ? 'Today' : \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
    </span>
    <input type="date" wire:model.live="selectedDate" class="eod-date-input" max="{{ now()->toDateString() }}">
</div>

{{-- Payment summary cards --}}
<div class="eod-cards">
    <div class="eod-card">
        <div class="eod-card-label">Cash</div>
        <div class="eod-card-val">₦{{ number_format($summary['cash'], 0) }}</div>
        <div class="eod-card-sub">physical handover</div>
    </div>
    <div class="eod-card">
        <div class="eod-card-label">Transfers</div>
        <div class="eod-card-val">₦{{ number_format($summary['transfer'], 0) }}</div>
        <div class="eod-card-sub">bank transfer</div>
    </div>
    <div class="eod-card">
        <div class="eod-card-label">Card</div>
        <div class="eod-card-val">₦{{ number_format($summary['card'], 0) }}</div>
        <div class="eod-card-sub">card payment</div>
    </div>
    <div class="eod-card">
        <div class="eod-card-label">POS Terminal</div>
        <div class="eod-card-val">₦{{ number_format($summary['pos'], 0) }}</div>
        <div class="eod-card-sub">terminal swipe</div>
    </div>
    <div class="eod-card total">
        <div class="eod-card-label">Total Received</div>
        <div class="eod-card-val">₦{{ number_format($summary['total'], 0) }}</div>
        <div class="eod-card-sub">{{ $summary['count'] }} payment{{ $summary['count'] === 1 ? '' : 's' }}</div>
    </div>
</div>

<div class="eod-grid">

    {{-- Left: outstanding + flags --}}
    <div>
        {{-- Outstanding orders --}}
        <div class="eod-panel">
            <div class="eod-panel-head">
                <span class="eod-panel-title">Outstanding Orders (Today)</span>
                @if($outstanding->count() > 0)
                <span class="eod-panel-badge">{{ $outstanding->count() }} unpaid</span>
                @else
                <span class="eod-panel-badge ok">All clear</span>
                @endif
            </div>
            <div class="eod-panel-body" style="padding:0 1rem;">
                @forelse($outstanding as $o)
                <div class="eod-order-row">
                    <span class="eod-order-ref">{{ $o->reference }}</span>
                    <span class="eod-order-name">{{ $o->customer_name }}</span>
                    <span class="eod-order-total">
                        ₦{{ number_format($o->total_amount, 0) }}
                        @if((float)$o->amount_paid > 0)
                        <br><span style="font-size:.7rem;font-weight:400;color:var(--text3);">paid ₦{{ number_format($o->amount_paid, 0) }}</span>
                        @endif
                    </span>
                    <span class="eod-order-status {{ $o->payment_status }}">{{ ucfirst($o->payment_status) }}</span>
                </div>
                @empty
                <p class="eod-empty">No outstanding orders for this date.</p>
                @endforelse
            </div>
        </div>

        {{-- Driver cash flag --}}
        <div class="eod-panel">
            <div class="eod-panel-head">
                <span class="eod-panel-title">Driver Cash Pending</span>
                @if($driverPending > 0)
                <span class="eod-panel-badge">{{ $driverPending }} unconfirmed</span>
                @else
                <span class="eod-panel-badge ok">All confirmed</span>
                @endif
            </div>
            <div class="eod-panel-body">
                @if($driverPending > 0)
                <p style="font-size:.83rem;color:var(--text2);margin:0;">
                    {{ $driverPending }} order{{ $driverPending === 1 ? '' : 's' }} still have cash collected by a driver
                    that you haven't confirmed receiving. Go to <strong>Orders</strong> and use
                    <em>Confirm Cash from Driver</em> on each.
                </p>
                @else
                <p style="font-size:.83rem;color:var(--text3);margin:0;">All driver cash has been confirmed.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: cash reconciliation --}}
    <div>
        <div class="eod-panel">
            <div class="eod-panel-head">
                <span class="eod-panel-title">Cash Reconciliation</span>
            </div>
            <div class="eod-panel-body">

                @if($existing)
                @php $es = $existing->discrepancy_status; @endphp
                <div class="eod-saved-banner {{ $es }}">
                    <span>{{ $es === 'balanced' ? '✓' : ($es === 'shortage' ? '↓' : '↑') }}</span>
                    <span>
                        Day closed by {{ $existing->closedBy?->name ?? 'you' }}
                        @if($es === 'balanced') — Cash balanced.
                        @elseif($es === 'shortage') — Shortage of ₦{{ number_format(abs($existing->discrepancy), 2) }}.
                        @else — Overage of ₦{{ number_format($existing->discrepancy, 2) }}.
                        @endif
                    </span>
                </div>
                @endif

                <div class="eod-recon-row">
                    <span class="eod-recon-label">System cash total</span>
                    <span class="eod-recon-val">₦{{ number_format($cashExpected, 2) }}</span>
                </div>
                <div class="eod-recon-row">
                    <span class="eod-recon-label">Transfers</span>
                    <span class="eod-recon-val">₦{{ number_format($summary['transfer'], 2) }}</span>
                </div>
                <div class="eod-recon-row">
                    <span class="eod-recon-label">Card + POS</span>
                    <span class="eod-recon-val">₦{{ number_format($summary['card'] + $summary['pos'], 2) }}</span>
                </div>
                <div class="eod-recon-divider"></div>

                <div class="eod-field">
                    <label>Physical cash count (₦)</label>
                    <input wire:model.live="cashCounted" type="number" step="0.01" min="0"
                        class="eod-input" placeholder="Enter amount in till…">
                    @error('cashCounted')<p class="eod-err">{{ $message }}</p>@enderror
                </div>

                @if($discrepancy !== null)
                <div class="eod-recon-divider"></div>
                <div class="eod-recon-row">
                    <span class="eod-recon-label">Discrepancy</span>
                    <span class="eod-recon-disc {{ $discStatus }}">
                        @if($discrepancy === 0.0)
                            Balanced ✓
                        @elseif($discrepancy > 0)
                            +₦{{ number_format($discrepancy, 2) }} overage
                        @else
                            −₦{{ number_format(abs($discrepancy), 2) }} short
                        @endif
                    </span>
                </div>
                @endif

                <div class="eod-field" style="margin-top:.85rem;">
                    <label>Notes (optional)</label>
                    <textarea wire:model.live="eodNotes" class="eod-input eod-textarea"
                        placeholder="e.g. ₦500 in change given, printer fault on receipt #…"></textarea>
                </div>

                <button type="button" wire:click="closeDay" class="eod-close-btn"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="closeDay">
                        {{ $existing ? 'Update Day Close' : 'Close Day' }}
                    </span>
                    <span wire:loading wire:target="closeDay">Saving…</span>
                </button>

            </div>
        </div>
    </div>

</div>

{{-- History --}}
<div class="eod-hist-wrap">
    <div class="eod-hist-head">
        <span class="eod-hist-head-title">Recent Reconciliations{{ $isAdmin ? ' — All Staff' : '' }}</span>
    </div>
    @if($history->isEmpty())
    <p class="eod-hist-empty">No reconciliations recorded yet.</p>
    @else
    <table class="eod-hist-table">
        <thead>
            <tr>
                <th>Date</th>
                @if($isAdmin)<th>Closed By</th>@endif
                <th>Cash Expected</th>
                <th>Cash Counted</th>
                <th>Transfers</th>
                <th>Card + POS</th>
                <th>Total</th>
                <th>Discrepancy</th>
                <th>Outstanding</th>
                <th>Driver ₦ Pending</th>
            </tr>
        </thead>
        <tbody>
            @foreach($history as $rec)
            @php $ds = $rec->discrepancy_status; @endphp
            <tr>
                <td><span class="eod-hist-date">{{ $rec->date->format('d M Y') }}</span></td>
                @if($isAdmin)<td><span class="eod-hist-by">{{ $rec->closedBy?->name ?? '—' }}</span></td>@endif
                <td class="eod-hist-amt">₦{{ number_format($rec->total_cash_expected, 0) }}</td>
                <td class="eod-hist-amt">₦{{ number_format($rec->total_cash_counted, 0) }}</td>
                <td class="eod-hist-amt">₦{{ number_format($rec->total_transfers, 0) }}</td>
                <td class="eod-hist-amt">₦{{ number_format((float)$rec->total_card + (float)$rec->total_pos, 0) }}</td>
                <td class="eod-hist-amt" style="color:var(--gold);font-weight:800;">₦{{ number_format($rec->total_all, 0) }}</td>
                <td>
                    <span class="eod-hist-disc {{ $ds }}">
                        @if($ds === 'balanced') Balanced
                        @elseif($ds === 'overage') +₦{{ number_format($rec->discrepancy, 2) }}
                        @else −₦{{ number_format(abs($rec->discrepancy), 2) }}
                        @endif
                    </span>
                </td>
                <td style="font-size:.78rem;color:var(--text3);">
                    {{ $rec->outstanding_orders_count > 0 ? $rec->outstanding_orders_count . ' order(s)' : '—' }}
                </td>
                <td style="font-size:.78rem;color:var(--text3);">
                    {{ $rec->pending_driver_cash_count > 0 ? $rec->pending_driver_cash_count . ' order(s)' : '—' }}
                </td>
            </tr>
            @if($rec->notes)
            <tr>
                <td colspan="{{ $isAdmin ? 10 : 9 }}" style="font-size:.75rem;color:var(--text3);padding:.25rem 1rem .5rem;border-bottom:1px solid var(--border);">
                    📝 {{ $rec->notes }}
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif
</div>

</x-filament-panels::page>
