<x-filament-panels::page>
<style>
:root {
    --bg:#ffffff;--bg2:#f9fafb;--bg3:#f3f4f6;--border:#e5e7eb;--border2:#d1d5db;
    --text:#111827;--text2:#374151;--text3:#6b7280;--muted:#9ca3af;
    --gold:#C9A84C;--gold-h:#b8943d;--green:#059669;--green-bg:rgba(16,185,129,.1);
}
.dark{--bg:#1f2937;--bg2:#111827;--bg3:#1a2535;--border:#374151;--border2:#4b5563;
    --text:#f9fafb;--text2:#e5e7eb;--text3:#d1d5db;--muted:#6b7280;
    --green:#34d399;--green-bg:rgba(16,185,129,.12);}

.q-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem;}
.q-card{background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:1.1rem;cursor:pointer;transition:border-color .15s,box-shadow .15s;position:relative;}
.q-card:hover{border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,168,76,.1);}
.q-card.is-done{border-color:rgba(16,185,129,.4);}
.q-top{display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem;margin-bottom:.4rem;}
.q-ref{font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);}
.q-tap-hint{font-size:.62rem;color:var(--muted);flex-shrink:0;opacity:.6;}
.q-name{font-size:1rem;font-weight:700;color:var(--text);margin:.1rem 0 .5rem;}
.q-badges{display:flex;gap:.4rem;flex-wrap:wrap;align-items:center;margin-bottom:.55rem;}
.q-stage-badge{font-size:.7rem;font-weight:700;text-transform:uppercase;padding:.18rem .5rem;border-radius:4px;}
.stage-finishing{background:rgba(8,145,178,.1);color:#0e7490;}
.dark .stage-finishing{color:#22d3ee;}
.q-cust{font-size:.82rem;color:var(--text2);margin-bottom:.5rem;display:flex;align-items:center;gap:.35rem;flex-wrap:wrap;}
.q-cust-name{font-weight:600;}
.q-cust-phone{font-size:.75rem;color:var(--text3);}
.q-notes{font-size:.78rem;color:var(--text3);background:var(--bg2);border:1px solid var(--border);border-radius:6px;padding:.4rem .55rem;margin-bottom:.6rem;}
.q-ts{font-size:.7rem;color:var(--muted);margin-bottom:.75rem;}
.qbtn-done{width:100%;padding:.5rem;border-radius:7px;font-size:.82rem;font-weight:700;font-family:inherit;cursor:pointer;border:none;background:var(--gold);color:#111827;transition:background .15s;position:relative;z-index:1;}
.qbtn-done:hover{background:var(--gold-h);}
.qbtn-done:disabled{opacity:.5;cursor:not-allowed;}
.done-chip{display:flex;align-items:center;gap:.35rem;padding:.45rem .65rem;background:var(--green-bg);border:1px solid rgba(16,185,129,.3);border-radius:7px;font-size:.8rem;font-weight:700;color:var(--green);}
.empty-state{text-align:center;padding:3rem;color:var(--muted);}

/* Details modal */
.dtl-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:50;display:flex;align-items:center;justify-content:center;padding:1rem;}
.dtl-box{background:var(--bg);border:1px solid var(--border);border-radius:12px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;padding:1.5rem;box-shadow:0 20px 40px rgba(0,0,0,.25);}
.dtl-title{font-size:1.1rem;font-weight:700;color:var(--text);margin-bottom:.1rem;}
.dtl-sub{font-size:.8rem;color:var(--text3);margin-bottom:1rem;}
.dtl-divider{border:none;border-top:1px solid var(--border);margin:.75rem 0;}
.dtl-section{margin-bottom:.9rem;}
.dtl-label{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.3rem;}
.dtl-value{font-size:.88rem;color:var(--text2);line-height:1.55;}
.dtl-contact{display:flex;gap:1.5rem;flex-wrap:wrap;}
.dtl-contact-item{display:flex;flex-direction:column;gap:.1rem;}
.dtl-contact-item small{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);}
.dtl-contact-item span{font-size:.9rem;font-weight:600;color:var(--text);}
.dtl-actions{display:flex;gap:.65rem;margin-top:1.1rem;}
.dtl-btn-close{flex:1;padding:.5rem;border-radius:7px;font-size:.82rem;font-weight:700;cursor:pointer;font-family:inherit;border:none;background:var(--bg3);color:var(--text2);transition:filter .15s;}
.dtl-btn-close:hover{filter:brightness(.95);}
.dtl-btn-done{flex:2;padding:.5rem;border-radius:7px;font-size:.82rem;font-weight:700;cursor:pointer;font-family:inherit;border:none;background:var(--gold);color:#111827;transition:background .15s;}
.dtl-btn-done:hover{background:var(--gold-h);}
.dtl-btn-done:disabled{opacity:.5;cursor:not-allowed;}
</style>

@php
    $assignments = $this->getMyAssignments();
    $showContact = \App\Models\AppSetting::bool('tailor_queue_show_customer_contact', false);
@endphp

@if($assignments->isEmpty())
<div class="empty-state">
    <div style="font-size:2.5rem;margin-bottom:.75rem;">✨</div>
    <p style="font-size:.95rem;">No finishing assignments for you right now.</p>
</div>
@else
<div class="q-grid">
    @foreach($assignments as $a)
    @php $item = $a->orderItem; $order = $a->order; @endphp
    <div class="q-card {{ $item?->staff_marked_done ? 'is-done' : '' }}"
         wire:click="openDetailsModal({{ $a->id }})"
         wire:key="fq-{{ $a->id }}"
         title="Tap to view details">
        <div class="q-top">
            <span class="q-ref">{{ $order?->reference ?? 'N/A' }}</span>
            <span class="q-tap-hint">tap for details →</span>
        </div>
        <div class="q-name">{{ $item?->description ?? 'Order-level task' }}</div>
        @if($item)
        <div class="q-badges">
            <span class="q-stage-badge stage-{{ $item->item_stage }}">{{ ucwords(str_replace('_',' ',$item->item_stage)) }}</span>
        </div>
        @endif
        @if($order)
        <div class="q-cust">
            <span class="q-cust-name">{{ $order->customer_name }}</span>
            @if($showContact && $order->customer_phone)
            <span class="q-cust-phone">· {{ $order->customer_phone }}</span>
            @endif
        </div>
        @endif
        @if($a->notes)<div class="q-notes">📝 {{ $a->notes }}</div>@endif
        <div class="q-ts">Assigned {{ $a->assigned_at->diffForHumans() }}</div>
        @if($item?->staff_marked_done)
        <div class="done-chip">✓ Done · {{ $item->staff_done_at?->diffForHumans() }}</div>
        @else
        <button wire:click.stop="markDone({{ $a->id }})"
            wire:loading.attr="disabled" wire:target="markDone({{ $a->id }})"
            class="qbtn-done">✓ Mark as Done</button>
        @endif
    </div>
    @endforeach
</div>
@endif

{{-- Details Modal --}}
@if($showDetailsModal)
@php
    $da = $assignments->firstWhere('id', $detailsAssignmentId);
    $di = $da?->orderItem; $do = $da?->order; $dc = $do?->customer;
@endphp
@if($da)
<div class="dtl-backdrop" wire:click.self="closeDetailsModal">
<div class="dtl-box">
    <div class="dtl-title">{{ $di?->description ?? 'Order-level task' }}</div>
    <div class="dtl-sub">{{ $do?->reference ?? '—' }}
        @if($di) · <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:#0e7490;">{{ ucwords(str_replace('_',' ',$di->item_stage)) }}</span>@endif
    </div>
    <hr class="dtl-divider">
    @if($do)
    <div class="dtl-section">
        <div class="dtl-label">Customer</div>
        <div class="dtl-contact">
            <div class="dtl-contact-item"><small>Name</small><span>{{ $do->customer_name }}</span></div>
            @if($showContact && $do->customer_phone)
            <div class="dtl-contact-item"><small>Phone</small><span>{{ $do->customer_phone }}</span></div>
            @endif
        </div>
    </div>
    <hr class="dtl-divider">
    @endif
    @if($di?->production_notes)
    <div class="dtl-section">
        <div class="dtl-label">Production Notes</div>
        <div class="dtl-value">{{ $di->production_notes }}</div>
    </div>
    @endif
    @if($da->notes)
    <div class="dtl-section">
        <div class="dtl-label">Notes from Supervisor</div>
        <div class="dtl-value">{{ $da->notes }}</div>
    </div>
    @endif
    <div class="dtl-actions">
        <button wire:click="closeDetailsModal" class="dtl-btn-close">Close</button>
        @if(! $di?->staff_marked_done)
        <button wire:click="markDone({{ $da->id }})"
            wire:loading.attr="disabled" wire:target="markDone({{ $da->id }})"
            class="dtl-btn-done">✓ Mark as Done</button>
        @else
        <div class="done-chip" style="flex:2;justify-content:center;">✓ Done · {{ $di->staff_done_at?->diffForHumans() }}</div>
        @endif
    </div>
</div>
</div>
@endif
@endif

</x-filament-panels::page>
