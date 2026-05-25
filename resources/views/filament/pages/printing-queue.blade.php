<x-filament-panels::page>
<style>
:root {
    --bg:#ffffff;--bg2:#f9fafb;--bg3:#f3f4f6;--border:#e5e7eb;--border2:#d1d5db;
    --text:#111827;--text2:#374151;--text3:#6b7280;--muted:#9ca3af;
    --gold:#C9A84C;--gold-h:#b8943d;--green:#059669;--green-bg:rgba(16,185,129,.1);
    --print:#ea580c;
}
.dark{--bg:#1f2937;--bg2:#111827;--bg3:#1a2535;--border:#374151;--border2:#4b5563;
    --text:#f9fafb;--text2:#e5e7eb;--text3:#d1d5db;--muted:#6b7280;
    --green:#34d399;--green-bg:rgba(16,185,129,.12);--print:#fb923c;}

.q-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem;}
.q-card{background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:1.1rem;cursor:pointer;transition:border-color .15s,box-shadow .15s;position:relative;}
.q-card:hover{border-color:var(--print);box-shadow:0 0 0 3px rgba(234,88,12,.08);}
.q-card.is-done{border-color:rgba(16,185,129,.4);}
.q-top{display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem;margin-bottom:.4rem;}
.q-ref{font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);}
.q-tap-hint{font-size:.62rem;color:var(--muted);flex-shrink:0;opacity:.6;}
.q-name{font-size:1rem;font-weight:700;color:var(--text);margin:.1rem 0 .5rem;}
.q-badges{display:flex;gap:.4rem;flex-wrap:wrap;align-items:center;margin-bottom:.55rem;}
.q-stage-badge{font-size:.7rem;font-weight:700;text-transform:uppercase;padding:.18rem .5rem;border-radius:4px;background:rgba(234,88,12,.1);color:var(--print);}
.q-cust{font-size:.82rem;color:var(--text2);margin-bottom:.4rem;display:flex;align-items:center;gap:.35rem;flex-wrap:wrap;}
.q-cust-name{font-weight:600;}
.q-cust-phone{font-size:.75rem;color:var(--text3);}
.q-design-thumb{width:100%;aspect-ratio:2/1;object-fit:cover;border-radius:6px;border:1px solid var(--border);margin-bottom:.5rem;}
.q-design-hint{font-size:.72rem;color:var(--print);font-weight:600;margin-bottom:.35rem;}
.q-notes{font-size:.78rem;color:var(--text3);background:var(--bg2);border:1px solid var(--border);border-radius:6px;padding:.4rem .55rem;margin-bottom:.5rem;}
.q-ts{font-size:.7rem;color:var(--muted);margin-bottom:.75rem;}
.qbtn-done{width:100%;padding:.5rem;border-radius:7px;font-size:.82rem;font-weight:700;font-family:inherit;cursor:pointer;border:none;background:var(--gold);color:#111827;transition:background .15s;position:relative;z-index:1;}
.qbtn-done:hover{background:var(--gold-h);}
.qbtn-done:disabled{opacity:.5;cursor:not-allowed;}
.done-chip{display:flex;align-items:center;gap:.35rem;padding:.45rem .65rem;background:var(--green-bg);border:1px solid rgba(16,185,129,.3);border-radius:7px;font-size:.8rem;font-weight:700;color:var(--green);}
.empty-state{text-align:center;padding:3rem;color:var(--muted);}

/* Details modal */
.dtl-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:50;display:flex;align-items:center;justify-content:center;padding:1rem;}
.dtl-box{background:var(--bg);border:1px solid var(--border);border-radius:12px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;padding:1.5rem;box-shadow:0 20px 40px rgba(0,0,0,.25);}
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
.dtl-design-img{width:100%;max-height:300px;object-fit:contain;border-radius:8px;border:1px solid var(--border);display:block;margin-bottom:.4rem;}
.dtl-file-link{font-size:.83rem;color:#6366f1;text-decoration:none;}
.dtl-file-link:hover{text-decoration:underline;}
.dtl-actions{display:flex;gap:.65rem;margin-top:1.1rem;}
.dtl-btn-close{flex:1;padding:.5rem;border-radius:7px;font-size:.82rem;font-weight:700;cursor:pointer;font-family:inherit;border:none;background:var(--bg3);color:var(--text2);transition:filter .15s;}
.dtl-btn-close:hover{filter:brightness(.95);}
.dtl-btn-print{padding:.5rem 1rem;border-radius:7px;font-size:.82rem;font-weight:700;cursor:pointer;font-family:inherit;border:1px solid var(--border2);background:var(--bg2);color:var(--text2);text-decoration:none;display:inline-flex;align-items:center;gap:.35rem;}
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
    <div style="font-size:2.5rem;margin-bottom:.75rem;">🖨️</div>
    <p style="font-size:.95rem;">No print jobs assigned to you right now.</p>
</div>
@else
<div class="q-grid">
    @foreach($assignments as $a)
    @php
        $item  = $a->orderItem;
        $order = $a->order;
        $dfUrl   = $item?->design_file ? \Storage::url($item->design_file) : null;
        $dfThumb = $item?->design_file ? \App\Support\DesignFileProcessor::thumbPath($item->design_file) : null;
        $dfThumbUrl = $dfThumb ? \Storage::url($dfThumb) : $dfUrl;
        $dfExt   = $item?->design_file ? strtolower(pathinfo($item->design_file, PATHINFO_EXTENSION)) : '';
        $dfImg   = in_array($dfExt, ['jpg','jpeg','png','webp','gif','bmp']);
    @endphp
    <div class="q-card {{ $item?->staff_marked_done ? 'is-done' : '' }}"
         wire:click="openDetailsModal({{ $a->id }})"
         wire:key="pq-{{ $a->id }}"
         title="Tap to view print details">
        <div class="q-top">
            <span class="q-ref">{{ $order?->reference ?? 'N/A' }}</span>
            <span class="q-tap-hint">tap for details →</span>
        </div>

        {{-- Design image thumbnail on card for quick reference --}}
        @if($dfImg && $dfUrl)
        <img src="{{ $dfThumbUrl }}" alt="Design" class="q-design-thumb"
             onerror="this.style.display='none'">
        @elseif($item?->design_file)
        <div class="q-design-hint">📎 Design file attached</div>
        @elseif($item?->design_notes)
        <div class="q-design-hint">📝 Design notes provided</div>
        @else
        <div class="q-design-hint" style="color:var(--muted);">⚠ No design file — check with cashier</div>
        @endif

        <div class="q-name">{{ $item?->description ?? 'Order-level task' }}</div>
        <div class="q-badges">
            <span class="q-stage-badge">Printing</span>
        </div>
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
        <div class="done-chip">✓ Print done · {{ $item->staff_done_at?->diffForHumans() }}</div>
        @else
        <button wire:click.stop="markDone({{ $a->id }})"
            wire:loading.attr="disabled" wire:target="markDone({{ $a->id }})"
            class="qbtn-done">✓ Mark Print as Done</button>
        @endif
    </div>
    @endforeach
</div>
@endif

{{-- Details Modal --}}
@if($showDetailsModal)
@php
    $da   = $assignments->firstWhere('id', $detailsAssignmentId);
    $di   = $da?->orderItem; $do = $da?->order; $dc = $do?->customer;
    $mDfUrl = $di?->design_file ? \Storage::url($di->design_file) : null;
    $mDfExt = $di?->design_file ? strtolower(pathinfo($di->design_file, PATHINFO_EXTENSION)) : '';
    $mDfImg = in_array($mDfExt, ['jpg','jpeg','png','webp','gif','bmp']);
@endphp
@if($da)
<div class="dtl-backdrop" wire:click.self="closeDetailsModal">
<div class="dtl-box">
    <div class="dtl-title">{{ $di?->description ?? 'Print Job' }}</div>
    <div class="dtl-sub">{{ $do?->reference ?? '—' }}</div>
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

    {{-- Design file — primary focus for printers --}}
    @if($mDfUrl)
    <div class="dtl-section">
        <div class="dtl-label">🖨 Design Artwork</div>
        @if($mDfImg)
        <a href="{{ $mDfUrl }}" target="_blank">
            <img src="{{ $mDfUrl }}" alt="Design" class="dtl-design-img"
                 onerror="this.style.display='none'">
        </a>
        @endif
        <a href="{{ $mDfUrl }}" target="_blank" class="dtl-file-link">
            📎 {{ basename($di->design_file) }}
        </a>
    </div>
    @else
    <div class="dtl-section">
        <div class="dtl-label">🖨 Design Artwork</div>
        <div class="dtl-value" style="color:var(--muted);font-style:italic;">No file uploaded — see design notes below.</div>
    </div>
    @endif

    @if($di?->design_notes)
    <div class="dtl-section">
        <div class="dtl-label">Design Notes</div>
        <div class="dtl-value">{{ $di->design_notes }}</div>
    </div>
    @endif

    @if($di?->production_notes)
    <div class="dtl-section">
        <div class="dtl-label">Production Notes</div>
        <div class="dtl-value">{{ $di->production_notes }}</div>
    </div>
    @endif

    @if($da->notes)
    <hr class="dtl-divider">
    <div class="dtl-section">
        <div class="dtl-label">Notes from Supervisor</div>
        <div class="dtl-value">{{ $da->notes }}</div>
    </div>
    @endif

    <div class="dtl-actions">
        <button wire:click="closeDetailsModal" class="dtl-btn-close">Close</button>
        @if($di)
        <a href="{{ route('print.order-item', $di->id) }}" target="_blank" class="dtl-btn-print">🖨 Print</a>
        @endif
        @if(! $di?->staff_marked_done)
        <button wire:click="markDone({{ $da->id }})"
            wire:loading.attr="disabled" wire:target="markDone({{ $da->id }})"
            class="dtl-btn-done">✓ Mark Print as Done</button>
        @else
        <div class="done-chip" style="flex:2;justify-content:center;">✓ Done · {{ $di->staff_done_at?->diffForHumans() }}</div>
        @endif
    </div>
</div>
</div>
@endif
@endif

</x-filament-panels::page>
