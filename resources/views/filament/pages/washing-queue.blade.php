<x-filament-panels::page>
<style>
:root {
    --bg:      #ffffff; --bg2:     #f9fafb; --bg3:     #f3f4f6;
    --border:  #e5e7eb;
    --text:    #111827; --text3:   #6b7280; --muted:   #9ca3af;
    --gold:    #C9A84C; --gold-h:  #b8943d;
    --green:   #059669; --green-bg: rgba(16,185,129,.1);
}
.dark {
    --bg:      #1f2937; --bg2:     #111827; --bg3:     #1a2535;
    --border:  #374151;
    --text:    #f9fafb; --text3:   #d1d5db; --muted:   #6b7280;
    --green:   #34d399; --green-bg: rgba(16,185,129,.12);
}

.q-grid  { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1rem; }
.q-card  { background:var(--bg); border:1px solid var(--border); border-radius:10px; padding:1.1rem; }
.q-ref   { font-size:.65rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:var(--muted); }
.q-name  { font-size:.95rem; font-weight:700; color:var(--text); margin:.2rem 0 .1rem; }
.q-meta  { font-size:.78rem; color:var(--text3); margin-bottom:.75rem; }
.q-ts    { font-size:.72rem; color:var(--text3); }
.q-notes { font-size:.78rem; color:var(--text3); background:var(--bg2); border:1px solid var(--border); border-radius:6px; padding:.4rem .55rem; margin:.4rem 0; }

.q-stage-row   { display:flex; align-items:center; gap:.5rem; margin-bottom:.5rem; }
.q-stage-lbl   { font-size:.65rem; font-weight:700; text-transform:uppercase; color:var(--muted); }
.q-stage-badge { font-size:.72rem; font-weight:700; text-transform:uppercase; padding:.18rem .5rem; border-radius:4px; }
.stage-sewing     { background:rgba(99,102,241,.12); color:#6366f1; }
.stage-embroidery { background:rgba(168,85,247,.12); color:#a855f7; }
.stage-finishing  { background:rgba(245,158,11,.12); color:#d97706; }
.stage-pending    { background:var(--bg3); color:var(--muted); }
.dark .stage-sewing     { color:#818cf8; }
.dark .stage-embroidery { color:#c084fc; }
.dark .stage-finishing  { color:#fbbf24; }

.qbtn-done {
    width:100%; margin-top:.85rem; padding:.5rem; border-radius:7px;
    font-size:.82rem; font-weight:700; font-family:inherit; cursor:pointer; border:none;
    background:var(--gold); color:#111827; transition:background .15s;
}
.qbtn-done:hover { background:var(--gold-h); }
.qbtn-done:disabled { opacity:.5; cursor:not-allowed; }

.done-chip {
    display:flex; align-items:center; gap:.35rem; margin-top:.85rem; padding:.45rem .65rem;
    background:var(--green-bg); border:1px solid rgba(16,185,129,.3); border-radius:7px;
    font-size:.8rem; font-weight:700; color:var(--green);
}

.empty-state { text-align:center; padding:3rem; color:var(--muted); }
</style>

@php $assignments = $this->getMyAssignments(); @endphp

@if($assignments->isEmpty())
<div class="empty-state">
    <div style="font-size:2.5rem; margin-bottom:.75rem;">✨</div>
    <p style="font-size:.95rem;">No assignments for you right now.</p>
</div>
@else
<div class="q-grid">
    @foreach($assignments as $a)
    @php $item = $a->orderItem; $order = $a->order; @endphp
    <div class="q-card" wire:key="wq-{{ $a->id }}">
        <div class="q-ref">{{ $order?->reference ?? 'N/A' }}</div>
        <div class="q-name">{{ $item?->description ?? 'Order-level task' }}</div>
        <div class="q-meta">
            {{ $order?->customer_name }}
            @if($order?->customer_phone) · {{ $order->customer_phone }}@endif
        </div>

        @if($item)
        <div class="q-stage-row">
            <span class="q-stage-lbl">Stage:</span>
            <span class="q-stage-badge stage-{{ $item->item_stage }}">{{ ucwords(str_replace('_',' ',$item->item_stage)) }}</span>
        </div>
        @endif

        @if($a->notes)
        <div class="q-notes">📝 {{ $a->notes }}</div>
        @endif

        <div class="q-ts">Assigned {{ $a->assigned_at->diffForHumans() }}</div>

        @if($item?->staff_marked_done)
        <div class="done-chip">✓ Marked as done · {{ $item->staff_done_at?->diffForHumans() }}</div>
        @else
        <button wire:click="markDone({{ $a->id }})" class="qbtn-done"
            wire:loading.attr="disabled" wire:target="markDone({{ $a->id }})">
            ✓ Mark as Done
        </button>
        @endif
    </div>
    @endforeach
</div>
@endif
</x-filament-panels::page>
