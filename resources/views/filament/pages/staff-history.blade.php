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

/* Stats row */
.sh-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
@media(max-width:640px){ .sh-stats{grid-template-columns:repeat(2,1fr);} }
.sh-stat {
    background:var(--bg); border:1px solid var(--border); border-radius:var(--radius);
    padding:1rem 1.1rem;
}
.sh-stat-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.12em; color:var(--muted); margin-bottom:.3rem; }
.sh-stat-val   { font-size:1.6rem; font-weight:800; color:var(--gold); line-height:1; }
.sh-stat-sub   { font-size:.72rem; color:var(--text3); margin-top:.2rem; }

/* Toolbar */
.sh-toolbar {
    display:flex; flex-wrap:wrap; gap:.6rem; align-items:center;
    background:var(--bg); border:1px solid var(--border); border-radius:var(--radius);
    padding:.75rem 1rem; margin-bottom:1rem;
}
.sh-search {
    flex:1; min-width:160px; position:relative;
}
.sh-search input {
    width:100%; padding:.45rem .75rem .45rem 2rem; border:1px solid var(--border2);
    border-radius:7px; background:var(--bg2); font-size:.85rem; outline:none;
    font-family:inherit; color:var(--text); transition:border-color .15s; box-sizing:border-box;
}
.sh-search input:focus { border-color:var(--gold); }
.sh-search-icon { position:absolute; left:.6rem; top:50%; transform:translateY(-50%); color:var(--muted); font-size:.9rem; }
.sh-select {
    padding:.45rem .65rem; border:1px solid var(--border2); border-radius:7px;
    background:var(--bg2); font-size:.82rem; color:var(--text); outline:none;
    font-family:inherit; cursor:pointer;
}
.sh-input {
    padding:.45rem .65rem; border:1px solid var(--border2); border-radius:7px;
    background:var(--bg2); font-size:.82rem; color:var(--text); outline:none; font-family:inherit;
}
.sh-clear {
    font-size:.78rem; color:var(--muted); background:none; border:none;
    cursor:pointer; font-family:inherit; padding:.3rem .5rem; border-radius:5px;
    transition:color .15s;
}
.sh-clear:hover { color:var(--text); }

/* Table */
.sh-table-wrap { background:var(--bg); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; }
.sh-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.sh-table th {
    text-align:left; font-size:.65rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.1em; color:var(--muted); padding:.65rem 1rem;
    border-bottom:1px solid var(--border); background:var(--bg2); white-space:nowrap;
}
.sh-table td { padding:.7rem 1rem; border-bottom:1px solid var(--border); vertical-align:middle; }
.sh-table tr:last-child td { border-bottom:none; }
.sh-table tr:hover td { background:var(--bg2); }

.sh-stage-pill {
    display:inline-block; font-size:.65rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.06em; padding:.18rem .55rem; border-radius:999px; white-space:nowrap;
}
.sh-ref   { font-size:.75rem; font-weight:700; color:var(--text3); }
.sh-name  { font-weight:600; color:var(--text); }
.sh-cust  { font-size:.78rem; color:var(--text3); }
.sh-time  { font-size:.75rem; color:var(--muted); white-space:nowrap; }
.sh-dur   { font-size:.75rem; color:var(--text3); white-space:nowrap; }

/* Empty */
.sh-empty { text-align:center; padding:3rem; color:var(--muted); }

/* Pagination */
.sh-pagination { padding:.75rem 1rem; border-top:1px solid var(--border); }
</style>

@php
    $stats   = $this->getStats();
    $history = $this->getHistory();

    $stageColors = [
        'tailor'      => ['bg' => 'rgba(99,102,241,.12)',  'text' => '#6366f1', 'label' => 'Sewing'],
        'sewing'      => ['bg' => 'rgba(99,102,241,.12)',  'text' => '#6366f1', 'label' => 'Sewing'],
        'embroidery'  => ['bg' => 'rgba(168,85,247,.12)',  'text' => '#a855f7', 'label' => 'Embroidery'],
        'printer'     => ['bg' => 'rgba(59,130,246,.12)',  'text' => '#3b82f6', 'label' => 'Printing'],
        'printing'    => ['bg' => 'rgba(59,130,246,.12)',  'text' => '#3b82f6', 'label' => 'Printing'],
        'finishing'   => ['bg' => 'rgba(245,158,11,.12)',  'text' => '#d97706', 'label' => 'Finishing'],
        'dry_cleaner' => ['bg' => 'rgba(20,184,166,.12)',  'text' => '#14b8a6', 'label' => 'Washing'],
        'washing'     => ['bg' => 'rgba(20,184,166,.12)',  'text' => '#14b8a6', 'label' => 'Washing'],
        'delivery'    => ['bg' => 'rgba(34,197,94,.12)',   'text' => '#16a34a', 'label' => 'Delivery'],
    ];
@endphp

{{-- Stats --}}
<div class="sh-stats">
    <div class="sh-stat">
        <div class="sh-stat-label">Total completed</div>
        <div class="sh-stat-val">{{ number_format($stats['total']) }}</div>
        <div class="sh-stat-sub">all time</div>
    </div>
    <div class="sh-stat">
        <div class="sh-stat-label">This week</div>
        <div class="sh-stat-val">{{ $stats['this_week'] }}</div>
        <div class="sh-stat-sub">{{ now()->startOfWeek()->format('d M') }} – {{ now()->endOfWeek()->format('d M') }}</div>
    </div>
    <div class="sh-stat">
        <div class="sh-stat-label">This month</div>
        <div class="sh-stat-val">{{ $stats['this_month'] }}</div>
        <div class="sh-stat-sub">{{ now()->format('F Y') }}</div>
    </div>
    <div class="sh-stat">
        <div class="sh-stat-label">Daily average</div>
        <div class="sh-stat-val">{{ $stats['avg_per_day'] }}</div>
        <div class="sh-stat-sub">items / day</div>
    </div>
</div>

{{-- Toolbar --}}
<div class="sh-toolbar">
    <div class="sh-search">
        <span class="sh-search-icon">⌕</span>
        <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search order ref, customer, item…">
    </div>

    <select wire:model.live="dept" class="sh-select">
        <option value="">All stages</option>
        <option value="tailor">Sewing</option>
        <option value="embroidery">Embroidery</option>
        <option value="printer">Printing</option>
        <option value="dry_cleaner">Washing / Finishing</option>
        <option value="delivery">Delivery</option>
    </select>

    <input wire:model.live="dateFrom" type="date" class="sh-input" title="From date">
    <span style="font-size:.8rem;color:var(--muted);">—</span>
    <input wire:model.live="dateTo" type="date" class="sh-input" title="To date">

    @if($search || $dept || $dateFrom || $dateTo)
        <button wire:click="$set('search',''); $set('dept',''); $set('dateFrom',''); $set('dateTo','')"
                class="sh-clear">✕ Clear</button>
    @endif
</div>

{{-- Table --}}
<div class="sh-table-wrap">
    @if($history->isEmpty())
        <div class="sh-empty">
            <div style="font-size:2rem;margin-bottom:.5rem;">🕐</div>
            <div>No completed work found{{ $search || $dept || $dateFrom || $dateTo ? ' for these filters' : ' yet' }}.</div>
        </div>
    @else
        <table class="sh-table">
            <thead>
                <tr>
                    <th>Stage</th>
                    <th>Item</th>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Assigned</th>
                    <th>Completed</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $row)
                    @php
                        $dept  = $row->department ?? 'sewing';
                        $color = $stageColors[$dept] ?? ['bg' => 'rgba(107,114,128,.1)', 'text' => '#6b7280', 'label' => ucfirst($dept)];
                        $item  = $row->orderItem;
                        $order = $row->order;
                        $duration = ($row->assigned_at && $row->completed_at)
                            ? $row->assigned_at->diff($row->completed_at)->format('%hh %im')
                            : '—';
                    @endphp
                    <tr>
                        <td>
                            <span class="sh-stage-pill"
                                  style="background:{{ $color['bg'] }};color:{{ $color['text'] }};">
                                {{ $color['label'] }}
                            </span>
                        </td>
                        <td>
                            <div class="sh-name">{{ $item?->description ?? '—' }}</div>
                            @if($item?->product)
                                <div class="sh-cust">{{ $item->product->name }}</div>
                            @endif
                        </td>
                        <td><span class="sh-ref">{{ $order?->reference ?? '—' }}</span></td>
                        <td>
                            <div class="sh-name" style="font-weight:500;">{{ $order?->customer?->name ?? $order?->customer_name ?? '—' }}</div>
                            @if($order?->customer?->phone)
                                <div class="sh-cust">{{ $order->customer->phone }}</div>
                            @endif
                        </td>
                        <td><span class="sh-time">{{ $row->assigned_at?->format('d M Y, H:i') ?? '—' }}</span></td>
                        <td><span class="sh-time" style="color:#16a34a;font-weight:600;">{{ $row->completed_at?->format('d M Y, H:i') ?? '—' }}</span></td>
                        <td><span class="sh-dur">{{ $duration }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($history->hasPages())
            <div class="sh-pagination">
                {{ $history->links() }}
            </div>
        @endif
    @endif
</div>

</x-filament-panels::page>
