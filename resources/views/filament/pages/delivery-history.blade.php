<x-filament-panels::page>
<style>
:root {
    --bg:     #ffffff; --bg2:    #f9fafb; --bg3:    #f3f4f6;
    --border: #e5e7eb; --border2:#d1d5db;
    --text:   #111827; --text2:  #374151; --text3:  #6b7280; --muted: #9ca3af;
    --gold:   #C9A84C; --gold-h: #b8943d;
    --radius: 10px;
}
.dark {
    --bg:     #1f2937; --bg2:    #111827; --bg3:    #1a2535;
    --border: #374151; --border2:#4b5563;
    --text:   #f9fafb; --text2:  #e5e7eb; --text3:  #d1d5db; --muted: #6b7280;
}
.dh-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
@media(max-width:640px){ .dh-stats{grid-template-columns:repeat(2,1fr);} }
.dh-stat { background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); padding:1rem 1.1rem; }
.dh-stat-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.12em; color:var(--muted); margin-bottom:.3rem; }
.dh-stat-val   { font-size:1.6rem; font-weight:800; color:var(--gold); line-height:1; }
.dh-stat-sub   { font-size:.72rem; color:var(--text3); margin-top:.2rem; }

.dh-toolbar {
    display:flex; flex-wrap:wrap; gap:.6rem; align-items:center;
    background:var(--bg); border:1px solid var(--border); border-radius:var(--radius);
    padding:.75rem 1rem; margin-bottom:1rem;
}
.dh-search { flex:1; min-width:160px; position:relative; }
.dh-search input {
    width:100%; padding:.45rem .75rem .45rem 2rem; border:1px solid var(--border2);
    border-radius:7px; background:var(--bg2); font-size:.85rem; outline:none;
    font-family:inherit; color:var(--text); transition:border-color .15s; box-sizing:border-box;
}
.dh-search input:focus { border-color:var(--gold); }
.dh-search-icon { position:absolute; left:.6rem; top:50%; transform:translateY(-50%); color:var(--muted); }
.dh-select { padding:.45rem .65rem; border:1px solid var(--border2); border-radius:7px; background:var(--bg2); font-size:.82rem; color:var(--text); outline:none; font-family:inherit; cursor:pointer; }
.dh-input  { padding:.45rem .65rem; border:1px solid var(--border2); border-radius:7px; background:var(--bg2); font-size:.82rem; color:var(--text); outline:none; font-family:inherit; }
.dh-clear  { font-size:.78rem; color:var(--muted); background:none; border:none; cursor:pointer; font-family:inherit; padding:.3rem .5rem; border-radius:5px; transition:color .15s; }
.dh-clear:hover { color:var(--text); }

.dh-table-wrap { background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; }
.dh-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.dh-table th {
    text-align:left; font-size:.65rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.1em; color:var(--muted); padding:.65rem 1rem;
    border-bottom:1px solid var(--border); background:var(--bg2); white-space:nowrap;
}
.dh-table th.sortable { cursor:pointer; user-select:none; }
.dh-table th.sortable:hover { color:var(--text); }
.dh-sort-icon { margin-left:.25rem; opacity:.4; font-size:.7rem; }
.dh-sort-icon.active { opacity:1; color:var(--gold); }
.dh-table td { padding:.7rem 1rem; border-bottom:1px solid var(--border); vertical-align:middle; }
.dh-table tr:last-child td { border-bottom:none; }
.dh-table tr:hover td { background:var(--bg2); }
.dh-ref   { font-size:.75rem; font-weight:700; color:var(--text3); }
.dh-name  { font-weight:600; color:var(--text); }
.dh-sub   { font-size:.78rem; color:var(--text3); }
.dh-time  { font-size:.75rem; color:var(--muted); white-space:nowrap; }
.dh-empty { text-align:center; padding:3rem; color:var(--muted); }
.dh-pagination { padding:.75rem 1rem; border-top:1px solid var(--border); }
</style>

@php
    $isAdmin = $this->isAdmin();
    $stats   = $this->getStats();
    $history = $this->getHistory();

    $icon = fn($col) => $sortCol === $col ? ($sortDir === 'asc' ? '↑' : '↓') : '↕';
    $cls  = fn($col) => 'dh-sort-icon' . ($sortCol === $col ? ' active' : '');
@endphp

{{-- Stats --}}
<div class="dh-stats">
    <div class="dh-stat">
        <div class="dh-stat-label">Total delivered</div>
        <div class="dh-stat-val">{{ number_format($stats['total']) }}</div>
        <div class="dh-stat-sub">all time</div>
    </div>
    <div class="dh-stat">
        <div class="dh-stat-label">Today</div>
        <div class="dh-stat-val">{{ $stats['today'] }}</div>
        <div class="dh-stat-sub">{{ now()->format('d M Y') }}</div>
    </div>
    <div class="dh-stat">
        <div class="dh-stat-label">This week</div>
        <div class="dh-stat-val">{{ $stats['this_week'] }}</div>
        <div class="dh-stat-sub">{{ now()->startOfWeek()->format('d M') }} – {{ now()->endOfWeek()->format('d M') }}</div>
    </div>
    <div class="dh-stat">
        <div class="dh-stat-label">Cash collected</div>
        <div class="dh-stat-val" style="font-size:1.25rem;">₦{{ number_format($stats['cash_collected'], 0) }}</div>
        <div class="dh-stat-sub">all time</div>
    </div>
</div>

{{-- Toolbar --}}
<div class="dh-toolbar">
    <div class="dh-search">
        <span class="dh-search-icon">⌕</span>
        <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search ref, customer, address…">
    </div>

    @if($isAdmin)
    <select wire:model.live="driverId" class="dh-select">
        <option value="">All drivers</option>
        @foreach($this->getDriverList() as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
    </select>
    @endif

    <input wire:model.live="dateFrom" type="date" class="dh-input" title="From date">
    <span style="font-size:.8rem;color:var(--muted);">—</span>
    <input wire:model.live="dateTo" type="date" class="dh-input" title="To date">

    @if($search || $dateFrom || $dateTo || $driverId)
        <button wire:click="$set('search',''); $set('dateFrom',''); $set('dateTo',''); $set('driverId', null)"
                class="dh-clear">✕ Clear</button>
    @endif
</div>

{{-- Table --}}
<div class="dh-table-wrap">
    @if($history->isEmpty())
        <div class="dh-empty">
            <div style="font-size:2rem;margin-bottom:.5rem;">🚚</div>
            <div>No deliveries found{{ $search || $dateFrom || $dateTo ? ' for these filters' : ' yet' }}.</div>
        </div>
    @else
        <table class="dh-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th class="sortable" wire:click="sortBy('customer_name')">
                        Customer <span class="{{ $cls('customer_name') }}">{{ $icon('customer_name') }}</span>
                    </th>
                    <th>Address</th>
                    @if($isAdmin)<th>Driver</th>@endif
                    <th>Payment</th>
                    <th class="sortable" style="text-align:right;" wire:click="sortBy('total_amount')">
                        Amount <span class="{{ $cls('total_amount') }}">{{ $icon('total_amount') }}</span>
                    </th>
                    <th class="sortable" wire:click="sortBy('updated_at')">
                        Delivered <span class="{{ $cls('updated_at') }}">{{ $icon('updated_at') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $order)
                    @php
                        $psBg  = $order->payment_status === 'paid' ? 'rgba(34,197,94,.12)' : ($order->payment_status === 'partial' ? 'rgba(245,158,11,.12)' : 'rgba(239,68,68,.1)');
                        $psTxt = $order->payment_status === 'paid' ? '#16a34a' : ($order->payment_status === 'partial' ? '#d97706' : '#dc2626');
                    @endphp
                    <tr>
                        <td><span class="dh-ref">{{ $order->reference }}</span></td>
                        <td>
                            <div class="dh-name">{{ $order->customer_name }}</div>
                            <div class="dh-sub">{{ $order->customer_phone }}</div>
                        </td>
                        <td>
                            <div class="dh-sub">{{ $order->customer_address ? \Illuminate\Support\Str::limit($order->customer_address, 45) : '—' }}</div>
                        </td>
                        @if($isAdmin)
                        <td>
                            <div class="dh-name" style="font-weight:500;">{{ $order->deliveryUser?->name ?? '—' }}</div>
                        </td>
                        @endif
                        <td>
                            <span style="font-size:.65rem;font-weight:700;text-transform:uppercase;padding:.18rem .55rem;border-radius:999px;background:{{ $psBg }};color:{{ $psTxt }};">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <span style="font-weight:700;color:var(--text);">₦{{ number_format($order->total_amount, 0) }}</span>
                        </td>
                        <td><span class="dh-time">{{ $order->updated_at?->format('d M Y, H:i') }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($history->hasPages())
            <div class="dh-pagination">{{ $history->links() }}</div>
        @endif
    @endif
</div>
</x-filament-panels::page>
