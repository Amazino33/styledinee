<x-filament-panels::page>
<style>
:root {
    --bg:     #ffffff; --bg2:    #f9fafb; --bg3:    #f3f4f6;
    --border: #e5e7eb; --border2:#d1d5db;
    --text:   #111827; --text2:  #374151; --text3:  #6b7280; --muted: #9ca3af;
    --gold:   #C9A84C; --gold-h: #b8943d; --gold-light: rgba(201,168,76,.10);
    --radius: 10px;
}
.dark {
    --bg:     #1f2937; --bg2:    #111827; --bg3:    #1a2535;
    --border: #374151; --border2:#4b5563;
    --text:   #f9fafb; --text2:  #e5e7eb; --text3:  #d1d5db; --muted: #6b7280;
}
.ch-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
@media(max-width:640px){ .ch-stats{grid-template-columns:repeat(2,1fr);} }
.ch-stat { background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); padding:1rem 1.1rem; }
.ch-stat-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.12em; color:var(--muted); margin-bottom:.3rem; }
.ch-stat-val   { font-size:1.5rem; font-weight:800; color:var(--gold); line-height:1; }
.ch-stat-sub   { font-size:.72rem; color:var(--text3); margin-top:.2rem; }

.ch-toolbar {
    display:flex; flex-wrap:wrap; gap:.6rem; align-items:center;
    background:var(--bg); border:1px solid var(--border); border-radius:var(--radius);
    padding:.75rem 1rem; margin-bottom:1rem;
}
.ch-search { flex:1; min-width:160px; position:relative; }
.ch-search input {
    width:100%; padding:.45rem .75rem .45rem 2rem; border:1px solid var(--border2);
    border-radius:7px; background:var(--bg2); font-size:.85rem; outline:none;
    font-family:inherit; color:var(--text); transition:border-color .15s; box-sizing:border-box;
}
.ch-search input:focus { border-color:var(--gold); }
.ch-search-icon { position:absolute; left:.6rem; top:50%; transform:translateY(-50%); color:var(--muted); font-size:.9rem; }
.ch-select {
    padding:.45rem .65rem; border:1px solid var(--border2); border-radius:7px;
    background:var(--bg2); font-size:.82rem; color:var(--text); outline:none; font-family:inherit; cursor:pointer;
}
.ch-input {
    padding:.45rem .65rem; border:1px solid var(--border2); border-radius:7px;
    background:var(--bg2); font-size:.82rem; color:var(--text); outline:none; font-family:inherit;
}
.ch-clear {
    font-size:.78rem; color:var(--muted); background:none; border:none;
    cursor:pointer; font-family:inherit; padding:.3rem .5rem; border-radius:5px; transition:color .15s;
}
.ch-clear:hover { color:var(--text); }

.ch-table-wrap { background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; }
.ch-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.ch-table th {
    text-align:left; font-size:.65rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.1em; color:var(--muted); padding:.65rem 1rem;
    border-bottom:1px solid var(--border); background:var(--bg2); white-space:nowrap;
}
.ch-table th.sortable { cursor:pointer; user-select:none; }
.ch-table th.sortable:hover { color:var(--text); }
.ch-sort-icon { margin-left:.25rem; opacity:.4; font-size:.7rem; }
.ch-sort-icon.active { opacity:1; color:var(--gold); }
.ch-table td { padding:.7rem 1rem; border-bottom:1px solid var(--border); vertical-align:middle; }
.ch-table tr:last-child td { border-bottom:none; }
.ch-table tr:hover td { background:var(--bg2); }
.ch-method-pill {
    display:inline-block; font-size:.65rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.06em; padding:.18rem .55rem; border-radius:999px;
}
.ch-ref   { font-size:.75rem; font-weight:700; color:var(--text3); }
.ch-name  { font-weight:600; color:var(--text); }
.ch-sub   { font-size:.78rem; color:var(--text3); }
.ch-amount { font-weight:700; color:var(--text); }
.ch-time  { font-size:.75rem; color:var(--muted); white-space:nowrap; }
.ch-empty { text-align:center; padding:3rem; color:var(--muted); }
.ch-pagination { padding:.75rem 1rem; border-top:1px solid var(--border); }
</style>

@php
    $isAdmin = $this->isAdmin();
    $stats   = $this->getStats();
    $history = $this->getHistory();

    $methodColors = [
        'cash'       => ['bg' => 'rgba(34,197,94,.12)',   'text' => '#16a34a', 'label' => 'Cash'],
        'transfer'   => ['bg' => 'rgba(59,130,246,.12)',  'text' => '#2563eb', 'label' => 'Transfer'],
        'card'       => ['bg' => 'rgba(168,85,247,.12)',  'text' => '#9333ea', 'label' => 'Card'],
        'pos'        => ['bg' => 'rgba(245,158,11,.12)',  'text' => '#d97706', 'label' => 'POS'],
        'adjustment' => ['bg' => 'rgba(107,114,128,.12)', 'text' => '#6b7280', 'label' => 'Adjustment'],
    ];

    $icon = fn($col) => $sortCol === $col ? ($sortDir === 'asc' ? '↑' : '↓') : '↕';
    $cls  = fn($col) => 'ch-sort-icon' . ($sortCol === $col ? ' active' : '');
@endphp

{{-- Stats --}}
<div class="ch-stats">
    <div class="ch-stat">
        <div class="ch-stat-label">Total collected</div>
        <div class="ch-stat-val">₦{{ number_format($stats['total_collected'], 0) }}</div>
        <div class="ch-stat-sub">all time</div>
    </div>
    <div class="ch-stat">
        <div class="ch-stat-label">Today</div>
        <div class="ch-stat-val">₦{{ number_format($stats['today'], 0) }}</div>
        <div class="ch-stat-sub">{{ now()->format('d M Y') }}</div>
    </div>
    <div class="ch-stat">
        <div class="ch-stat-label">This week</div>
        <div class="ch-stat-val">₦{{ number_format($stats['this_week'], 0) }}</div>
        <div class="ch-stat-sub">{{ now()->startOfWeek()->format('d M') }} – {{ now()->endOfWeek()->format('d M') }}</div>
    </div>
    <div class="ch-stat">
        <div class="ch-stat-label">Total transactions</div>
        <div class="ch-stat-val">{{ number_format($stats['total_count']) }}</div>
        <div class="ch-stat-sub">payments recorded</div>
    </div>
</div>

{{-- Toolbar --}}
<div class="ch-toolbar">
    <div class="ch-search">
        <span class="ch-search-icon">⌕</span>
        <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search order ref, customer…">
    </div>

    @if($isAdmin)
    <select wire:model.live="cashierId" class="ch-select">
        <option value="">All cashiers</option>
        @foreach($this->getCashierList() as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
    </select>
    @endif

    <select wire:model.live="method" class="ch-select">
        <option value="">All methods</option>
        <option value="cash">Cash</option>
        <option value="transfer">Transfer</option>
        <option value="card">Card</option>
        <option value="pos">POS</option>
        <option value="adjustment">Adjustment</option>
    </select>

    <input wire:model.live="dateFrom" type="date" class="ch-input" title="From date">
    <span style="font-size:.8rem;color:var(--muted);">—</span>
    <input wire:model.live="dateTo" type="date" class="ch-input" title="To date">

    @if($search || $method || $dateFrom || $dateTo || $cashierId)
        <button wire:click="$set('search',''); $set('method',''); $set('dateFrom',''); $set('dateTo',''); $set('cashierId', null)"
                class="ch-clear">✕ Clear</button>
    @endif
</div>

{{-- Table --}}
<div class="ch-table-wrap">
    @if($history->isEmpty())
        <div class="ch-empty">
            <div style="font-size:2rem;margin-bottom:.5rem;">💳</div>
            <div>No transactions found{{ $search || $method || $dateFrom || $dateTo ? ' for these filters' : ' yet' }}.</div>
        </div>
    @else
        <table class="ch-table">
            <thead>
                <tr>
                    <th class="sortable" wire:click="sortBy('method')">
                        Method <span class="{{ $cls('method') }}">{{ $icon('method') }}</span>
                    </th>
                    <th>Order</th>
                    <th>Customer</th>
                    @if($isAdmin)<th>Cashier</th>@endif
                    <th class="sortable" style="text-align:right;" wire:click="sortBy('amount')">
                        Amount <span class="{{ $cls('amount') }}">{{ $icon('amount') }}</span>
                    </th>
                    <th>Notes</th>
                    <th class="sortable" wire:click="sortBy('created_at')">
                        Date <span class="{{ $cls('created_at') }}">{{ $icon('created_at') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $payment)
                    @php
                        $color = $methodColors[$payment->method] ?? ['bg' => 'rgba(107,114,128,.12)', 'text' => '#6b7280', 'label' => ucfirst($payment->method)];
                        $order = $payment->order;
                    @endphp
                    <tr>
                        <td>
                            <span class="ch-method-pill"
                                  style="background:{{ $color['bg'] }};color:{{ $color['text'] }};">
                                {{ $color['label'] }}
                            </span>
                        </td>
                        <td>
                            <div class="ch-ref">{{ $order?->reference ?? '—' }}</div>
                            @if($order?->type)
                                <div class="ch-sub">{{ ucfirst(str_replace('_', ' ', $order->type)) }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="ch-name">{{ $order?->customer_name ?? '—' }}</div>
                            @if($order?->customer_phone)
                                <div class="ch-sub">{{ $order->customer_phone }}</div>
                            @endif
                        </td>
                        @if($isAdmin)
                        <td>
                            <div class="ch-name" style="font-weight:500;">{{ $payment->recordedBy?->name ?? '—' }}</div>
                        </td>
                        @endif
                        <td style="text-align:right;">
                            <span class="ch-amount">₦{{ number_format($payment->amount, 0) }}</span>
                        </td>
                        <td>
                            <span class="ch-sub">{{ $payment->notes ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="ch-time">{{ $payment->created_at?->format('d M Y, H:i') }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($history->hasPages())
            <div class="ch-pagination">
                {{ $history->links() }}
            </div>
        @endif
    @endif
</div>
</x-filament-panels::page>
