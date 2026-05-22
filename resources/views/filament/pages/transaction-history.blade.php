<x-filament-panels::page>
<style>
:root {
    --bg:      #ffffff; --bg2:    #f9fafb; --bg3:    #f3f4f6;
    --border:  #e5e7eb; --border2:#d1d5db;
    --text:    #111827; --text2:  #374151; --text3:  #6b7280; --muted: #9ca3af;
    --gold:    #C9A84C; --gold-h: #b8943d;
    --green:   #059669; --red: #dc2626; --amber: #d97706;
}
.dark {
    --bg:      #1f2937; --bg2:    #111827; --bg3:    #1a2535;
    --border:  #374151; --border2:#4b5563;
    --text:    #f9fafb; --text2:  #e5e7eb; --text3:  #d1d5db; --muted: #6b7280;
    --green:   #34d399; --red: #f87171; --amber: #fbbf24;
}

/* ── Summary cards ── */
.tx-summary { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:.75rem; margin-bottom:1.25rem; }
.tx-card    { background:var(--bg); border:1px solid var(--border); border-radius:10px; padding:1rem 1.1rem; }
.tx-card-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.35rem; }
.tx-card-value { font-size:1.35rem; font-weight:800; color:var(--text); line-height:1; }
.tx-card-value.green { color:var(--green); }
.tx-card-value.amber { color:var(--amber); }
.tx-card-value.red   { color:var(--red); }

/* ── Toolbar ── */
.tx-toolbar { display:flex; gap:.6rem; flex-wrap:wrap; align-items:center; margin-bottom:1rem; }
.tx-search-wrap { position:relative; flex:1; min-width:200px; }
.tx-search-wrap input {
    width:100%; padding:.45rem .75rem .45rem 2rem; border:1px solid var(--border2);
    border-radius:7px; background:var(--bg); font-size:.83rem; color:var(--text);
    font-family:inherit; outline:none; transition:border-color .15s;
}
.tx-search-wrap input:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.12); }
.tx-search-icon { position:absolute; left:.65rem; top:50%; transform:translateY(-50%); color:var(--muted); font-size:.85rem; pointer-events:none; }
.tx-select {
    padding:.43rem .65rem; border:1px solid var(--border2); border-radius:7px;
    background:var(--bg); font-size:.82rem; color:var(--text); font-family:inherit;
    outline:none; cursor:pointer; transition:border-color .15s;
}
.tx-select:focus { border-color:var(--gold); }
.tx-date { padding:.43rem .65rem; border:1px solid var(--border2); border-radius:7px; background:var(--bg); font-size:.82rem; color:var(--text); font-family:inherit; outline:none; }
.tx-date:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.12); }
.tx-reset { padding:.43rem .7rem; border:1px solid var(--border2); border-radius:7px; background:transparent; font-size:.78rem; font-weight:600; color:var(--text3); cursor:pointer; font-family:inherit; transition:all .15s; white-space:nowrap; }
.tx-reset:hover { border-color:var(--gold); color:var(--gold); }

/* ── Table ── */
.tx-wrap  { background:var(--bg); border:1px solid var(--border); border-radius:10px; overflow:hidden; }
.tx-table { width:100%; border-collapse:collapse; min-width:640px; }
.tx-table th { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); padding:.6rem 1rem; text-align:left; background:var(--bg2); border-bottom:1px solid var(--border); white-space:nowrap; }
.tx-table td { font-size:.83rem; color:var(--text2); padding:.7rem 1rem; border-bottom:1px solid var(--border); vertical-align:middle; }
.tx-table tbody tr:last-child td { border-bottom:none; }
.tx-table tbody tr:hover td { background:var(--bg2); }
.dark .tx-table tbody tr:hover td { background:rgba(255,255,255,.025); }

.tx-ref    { font-size:.65rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); display:block; }
.tx-cust   { font-weight:600; color:var(--text); font-size:.85rem; }
.tx-phone  { font-size:.72rem; color:var(--text3); display:block; margin-top:.1rem; }
.tx-amount { font-size:.92rem; font-weight:700; color:var(--green); }
.tx-date-cell { font-size:.78rem; color:var(--text3); white-space:nowrap; }
.tx-date-time { font-size:.68rem; color:var(--muted); display:block; }
.tx-recorder  { font-size:.78rem; color:var(--text3); }

/* ── Method badge ── */
.tx-method { display:inline-flex; align-items:center; font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; padding:.2rem .5rem; border-radius:4px; white-space:nowrap; }
.method-cash     { background:rgba(16,185,129,.1); color:#059669; border:1px solid rgba(16,185,129,.25); }
.method-transfer { background:rgba(59,130,246,.1); color:#1d4ed8; border:1px solid rgba(59,130,246,.25); }
.method-card     { background:rgba(139,92,246,.1); color:#6d28d9; border:1px solid rgba(139,92,246,.25); }
.method-pos      { background:rgba(245,158,11,.1); color:#b45309; border:1px solid rgba(245,158,11,.25); }
.method-adjustment { background:var(--bg3); color:var(--muted); border:1px solid var(--border); }
.dark .method-cash     { color:#34d399; }
.dark .method-transfer { color:#93c5fd; }
.dark .method-card     { color:#c4b5fd; }
.dark .method-pos      { color:#fcd34d; }

/* ── Notes ── */
.tx-notes { font-size:.73rem; color:var(--text3); font-style:italic; margin-top:.15rem; }

/* ── Pagination ── */
.tx-pagination { display:flex; justify-content:center; align-items:center; gap:.5rem; padding:.9rem 1rem; border-top:1px solid var(--border); background:var(--bg2); }
.tx-page-btn { padding:.28rem .65rem; border-radius:5px; font-size:.78rem; font-weight:600; cursor:pointer; border:1px solid var(--border2); background:transparent; color:var(--text3); font-family:inherit; transition:all .15s; }
.tx-page-btn:hover:not(:disabled) { border-color:var(--gold); color:var(--gold); }
.tx-page-btn:disabled { opacity:.4; cursor:not-allowed; }
.tx-page-info { font-size:.75rem; color:var(--muted); }

.empty-state { text-align:center; padding:3rem; color:var(--muted); }
</style>

@php
    $payments = $this->getPayments();
    $summary  = $this->getSummary();
    $hasFilters = $search || $methodFilter || $dateFrom || $dateTo;
@endphp

{{-- ── Summary Cards ── --}}
<div class="tx-summary">
    <div class="tx-card">
        <div class="tx-card-label">Total Collected (All Time)</div>
        <div class="tx-card-value green">₦{{ number_format($summary['total_collected'], 0) }}</div>
    </div>
    <div class="tx-card">
        <div class="tx-card-label">Collected Today</div>
        <div class="tx-card-value">₦{{ number_format($summary['today_collected'], 0) }}</div>
    </div>
    <div class="tx-card">
        <div class="tx-card-label">Outstanding Balance</div>
        <div class="tx-card-value red">₦{{ number_format($summary['total_outstanding'], 0) }}</div>
    </div>
    <div class="tx-card">
        <div class="tx-card-label">Orders with Balance</div>
        <div class="tx-card-value amber">{{ $summary['partial_count'] }}</div>
    </div>
</div>

{{-- ── Toolbar ── --}}
<div class="tx-toolbar">
    <div class="tx-search-wrap">
        <span class="tx-search-icon">⌕</span>
        <input
            wire:model.live.debounce.300ms="search"
            type="search"
            placeholder="Search by customer, phone, or order reference…"
            value="{{ $search }}">
    </div>

    <select wire:model.live="methodFilter" class="tx-select">
        <option value="">All Methods</option>
        <option value="cash">Cash</option>
        <option value="transfer">Bank Transfer</option>
        <option value="card">Card</option>
        <option value="pos">POS Terminal</option>
        <option value="adjustment">Adjustment</option>
    </select>

    <input wire:model.live="dateFrom" type="date" class="tx-date" title="From date" value="{{ $dateFrom }}">
    <input wire:model.live="dateTo"   type="date" class="tx-date" title="To date"   value="{{ $dateTo }}">

    @if($hasFilters)
    <button wire:click="$set('search',''); $set('methodFilter',''); $set('dateFrom',''); $set('dateTo','')" class="tx-reset">
        ✕ Clear
    </button>
    @endif
</div>

{{-- ── Table ── --}}
@if($payments->isEmpty())
<div class="empty-state">
    <div style="font-size:2.5rem; margin-bottom:.75rem;">💳</div>
    <p style="font-size:.95rem;">{{ $hasFilters ? 'No transactions match your filters.' : 'No transactions recorded yet.' }}</p>
</div>
@else
<div style="overflow-x:auto;">
<div class="tx-wrap">
<table class="tx-table">
    <thead>
        <tr>
            <th>Date &amp; Time</th>
            <th>Order</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Notes</th>
            <th>Recorded by</th>
        </tr>
    </thead>
    <tbody>
    @foreach($payments as $tx)
    @php $order = $tx->order; @endphp
    <tr wire:key="tx-{{ $tx->id }}">

        <td class="tx-date-cell">
            {{ $tx->created_at->format('d M Y') }}
            <span class="tx-date-time">{{ $tx->created_at->format('g:i A') }}</span>
        </td>

        <td>
            <span class="tx-ref">{{ $order?->reference ?? '—' }}</span>
            @if($order)
            <span style="font-size:.75rem; color:var(--text3);">
                {{ ucfirst(str_replace('_',' ', $order->payment_status)) }}
                · ₦{{ number_format($order->total_amount, 0) }} total
            </span>
            @endif
        </td>

        <td>
            <span class="tx-cust">{{ $order?->customer_name ?? '—' }}</span>
            @if($order?->customer_phone)
            <span class="tx-phone">{{ $order->customer_phone }}</span>
            @endif
        </td>

        <td>
            <span class="tx-amount">₦{{ number_format($tx->amount, 0) }}</span>
        </td>

        <td>
            @php
                $mClass = match($tx->method) {
                    'cash'       => 'method-cash',
                    'transfer'   => 'method-transfer',
                    'card'       => 'method-card',
                    'pos'        => 'method-pos',
                    default      => 'method-adjustment',
                };
            @endphp
            <span class="tx-method {{ $mClass }}">
                {{ \App\Models\Payment::methodLabel($tx->method) }}
            </span>
        </td>

        <td>
            @if($tx->notes)
            <span class="tx-notes">{{ $tx->notes }}</span>
            @else
            <span style="color:var(--muted);">—</span>
            @endif
        </td>

        <td class="tx-recorder">{{ $tx->recordedBy?->name ?? '—' }}</td>

    </tr>
    @endforeach
    </tbody>
</table>

@if($payments->hasPages())
<div class="tx-pagination">
    <button
        wire:click="previousPage"
        class="tx-page-btn"
        {{ $payments->onFirstPage() ? 'disabled' : '' }}>
        ← Prev
    </button>
    <span class="tx-page-info">
        Page {{ $payments->currentPage() }} of {{ $payments->lastPage() }}
        &nbsp;·&nbsp; {{ $payments->total() }} transactions
    </span>
    <button
        wire:click="nextPage"
        class="tx-page-btn"
        {{ ! $payments->hasMorePages() ? 'disabled' : '' }}>
        Next →
    </button>
</div>
@else
<div class="tx-pagination">
    <span class="tx-page-info">{{ $payments->total() }} {{ $payments->total() === 1 ? 'transaction' : 'transactions' }}</span>
</div>
@endif

</div>
</div>
@endif

</x-filament-panels::page>
