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

/* ── Sections ── */
.pt-sections   { display:flex; flex-direction:column; gap:1.25rem; }
.pt-section    { background:var(--bg); border:1px solid var(--border); border-radius:12px; overflow:hidden; }

.pt-sec-head   { display:flex; align-items:center; gap:.65rem; padding:.8rem 1.1rem; border-bottom:1px solid var(--border); }
.pt-sec-name   { font-size:.82rem; font-weight:800; text-transform:uppercase; letter-spacing:.1em; }
.pt-sec-role   { font-size:.7rem; color:var(--text3); }
.pt-sec-count  { margin-left:auto; font-size:.7rem; font-weight:700; color:var(--muted); background:var(--bg3); padding:.15rem .55rem; border-radius:99px; }

.sec-sewing     .pt-sec-name { color:#6366f1; }
.sec-embroidery .pt-sec-name { color:#a855f7; }
.sec-printing   .pt-sec-name { color:#ea580c; }
.sec-finishing  .pt-sec-name { color:#0891b2; }

.sec-sewing     .pt-sec-head { background:rgba(99,102,241,.04);  }
.sec-embroidery .pt-sec-head { background:rgba(168,85,247,.04);  }
.sec-printing   .pt-sec-head { background:rgba(234,88,12,.04);   }
.sec-finishing  .pt-sec-head { background:rgba(8,145,178,.04);   }
.dark .sec-sewing     .pt-sec-head { background:rgba(99,102,241,.07);  }
.dark .sec-embroidery .pt-sec-head { background:rgba(168,85,247,.07);  }
.dark .sec-printing   .pt-sec-head { background:rgba(234,88,12,.07);   }
.dark .sec-finishing  .pt-sec-head { background:rgba(8,145,178,.07);   }

/* ── Table ── */
.pt-wrap     { overflow-x:auto; }
.pt-table    { width:100%; border-collapse:collapse; min-width:600px; }
.pt-table th { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); padding:.6rem 1rem; text-align:left; background:var(--bg2); border-bottom:1px solid var(--border); white-space:nowrap; }
.pt-table td { font-size:.83rem; color:var(--text2); padding:.65rem 1rem; border-bottom:1px solid var(--border); vertical-align:middle; }
.pt-table tbody tr:last-child td { border-bottom:none; }
.pt-table tbody tr:hover td      { background:var(--bg2); }
.dark .pt-table tbody tr:hover td { background:rgba(255,255,255,.025); }

.pt-iname  { font-weight:600; color:var(--text); font-size:.85rem; }
.pt-ref    { display:block; font-size:.67rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); margin-top:.1rem; }
.pt-cust   { font-size:.8rem; }
.pt-unassigned { color:var(--muted); font-size:.78rem; font-style:italic; }
.pt-assignee   { font-weight:600; color:var(--text); }

/* ── Staff Done badge ── */
.badge-staff-done {
    display:inline-flex; align-items:center; gap:.2rem; font-size:.6rem; font-weight:700;
    text-transform:uppercase; padding:.15rem .45rem; border-radius:4px; letter-spacing:.06em;
    background:rgba(16,185,129,.15); color:#059669; border:1px solid rgba(16,185,129,.3);
    white-space:nowrap;
}
.dark .badge-staff-done { color:#34d399; background:rgba(16,185,129,.12); border-color:rgba(52,211,153,.25); }

.tr-done td { background:rgba(16,185,129,.025) !important; }
.dark .tr-done td { background:rgba(16,185,129,.05) !important; }

/* ── Action buttons ── */
.pt-actions { display:flex; gap:.35rem; align-items:center; flex-wrap:nowrap; }

.pt-btn-advance {
    padding:.28rem .65rem; border-radius:5px; font-size:.74rem; font-weight:700;
    cursor:pointer; border:none; background:#10b981; color:#fff;
    font-family:inherit; transition:background .15s; white-space:nowrap;
}
.pt-btn-advance:hover { background:#059669; }

.pt-btn-waiting {
    padding:.28rem .65rem; border-radius:5px; font-size:.74rem; font-weight:700;
    cursor:not-allowed; border:1px solid var(--border2); background:var(--bg3);
    color:var(--muted); font-family:inherit; white-space:nowrap;
}

.pt-btn-no-staff {
    padding:.28rem .65rem; border-radius:5px; font-size:.74rem; font-weight:700;
    cursor:not-allowed; border:1px solid #fcd34d; background:rgba(252,211,77,.1);
    color:#92400e; font-family:inherit; white-space:nowrap;
}
.dark .pt-btn-no-staff { color:#fbbf24; background:rgba(252,211,77,.08); border-color:rgba(252,211,77,.35); }

.pt-btn-reassign {
    padding:.28rem .6rem; border-radius:5px; font-size:.74rem; font-weight:700;
    cursor:pointer; border:1px solid var(--border2); background:transparent;
    color:var(--text3); font-family:inherit; transition:all .15s; white-space:nowrap;
}
.pt-btn-reassign:hover { border-color:var(--gold); color:var(--gold); }

.pt-btn-assign {
    padding:.28rem .65rem; border-radius:5px; font-size:.74rem; font-weight:700;
    cursor:pointer; border:1px dashed var(--border2); background:transparent;
    color:var(--muted); font-family:inherit; transition:all .15s; white-space:nowrap;
}
.pt-btn-assign:hover { border-color:var(--gold); color:var(--gold); border-style:solid; }

.pt-btn-details {
    padding:.28rem .6rem; border-radius:5px; font-size:.74rem; font-weight:600;
    cursor:pointer; border:1px solid var(--border2); background:transparent;
    color:var(--text3); font-family:inherit; transition:all .15s; white-space:nowrap;
}
.pt-btn-details:hover { border-color:#6366f1; color:#6366f1; }

/* ── Variant chip ── */
.pt-variant { display:inline-block; font-size:.65rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.05em; color:#a855f7; background:rgba(168,85,247,.1);
    border:1px solid rgba(168,85,247,.25); border-radius:4px; padding:.1rem .35rem; margin-top:.2rem; }

/* ── Empty row ── */
.pt-empty-row { padding:1.5rem 1rem; text-align:center; color:var(--muted); font-size:.83rem; font-style:italic; }

/* ── Details modal ── */
.details-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:50; display:flex; align-items:center; justify-content:center; padding:1rem; }
.details-box      { background:var(--bg); border:1px solid var(--border); border-radius:12px; width:100%; max-width:520px; max-height:88vh; overflow-y:auto; padding:1.5rem; box-shadow:0 20px 40px rgba(0,0,0,.25); }
.details-title    { font-size:1.05rem; font-weight:700; color:var(--text); margin-bottom:.15rem; }
.details-sub      { font-size:.8rem; color:var(--text3); margin-bottom:1.25rem; }
.details-section  { margin-bottom:1rem; }
.details-label    { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.3rem; }
.details-value    { font-size:.85rem; color:var(--text2); line-height:1.5; }
.details-meas     { display:grid; grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); gap:.4rem .75rem; margin-top:.4rem; }
.details-meas-item { font-size:.8rem; color:var(--text2); }
.details-meas-item strong { color:var(--text); }
.details-close    { width:100%; padding:.5rem; border-radius:7px; font-size:.82rem; font-weight:700; cursor:pointer; font-family:inherit; border:none; background:var(--bg3); color:var(--text2); transition:filter .15s; margin-top:.5rem; }
.details-close:hover { filter:brightness(.95); }
.details-file-link { font-size:.82rem; color:#6366f1; text-decoration:none; }
.details-file-link:hover { text-decoration:underline; }
.details-divider  { border:none; border-top:1px solid var(--border); margin:.75rem 0; }

/* ── Assignment modal ── */
.assign-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:50; display:flex; align-items:center; justify-content:center; padding:1rem; }
.assign-box      { background:var(--bg); border:1px solid var(--border); border-radius:12px; width:100%; max-width:380px; padding:1.5rem; box-shadow:0 20px 40px rgba(0,0,0,.25); }
.assign-title    { font-size:1rem; font-weight:700; color:var(--text); margin-bottom:.25rem; }
.assign-sub      { font-size:.8rem; color:var(--text3); margin-bottom:1.25rem; }
.assign-lbl      { display:block; font-size:.65rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); margin-bottom:.35rem; }
.assign-select   { width:100%; padding:.5rem .65rem; border:1px solid var(--border2); border-radius:7px; background:var(--bg2); font-size:.85rem; color:var(--text); font-family:inherit; outline:none; margin-bottom:.85rem; }
.assign-select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.15); }
.assign-notes    { width:100%; padding:.45rem .65rem; border:1px solid var(--border2); border-radius:7px; background:var(--bg2); font-size:.82rem; color:var(--text); font-family:inherit; outline:none; resize:none; margin-bottom:1rem; }
.assign-notes:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.15); }
.assign-actions  { display:flex; gap:.6rem; }
.assign-btn      { flex:1; padding:.5rem; border-radius:7px; font-size:.82rem; font-weight:700; cursor:pointer; font-family:inherit; border:none; transition:all .15s; }
.assign-cancel   { background:var(--bg3); color:var(--text2); }
.dark .assign-cancel { background:var(--border); }
.assign-cancel:hover { filter:brightness(.95); }
.assign-confirm  { background:var(--gold); color:#111827; }
.assign-confirm:hover { background:var(--gold-h); }
</style>

@php
    $stageItems     = $this->getItemsByStage();
    $showEmbroidery = $this->hasEmbroideryInTracker();
    $showPrinting   = $this->hasPrintingInTracker();
@endphp

<div class="pt-sections">
@foreach(\App\Filament\Pages\ProductionTracker::STAGE_DEFINITIONS as $stage => $def)
@if($stage === 'embroidery' && ! $showEmbroidery) @continue @endif
@if($stage === 'printing'   && ! $showPrinting)   @continue @endif
@php
    $sectionItems = $stageItems[$stage] ?? collect();
    $count        = $sectionItems->count();
@endphp
<div class="pt-section sec-{{ $stage }}">

    <div class="pt-sec-head">
        <span class="pt-sec-name">{{ $def['label'] }}</span>
        <span class="pt-sec-role">{{ $def['role_label'] }} only</span>
        @if($count > 0)
        <span class="pt-sec-count">{{ $count }} {{ $count === 1 ? 'item' : 'items' }}</span>
        @endif
    </div>

    @if($sectionItems->isEmpty())
    <div class="pt-empty-row">No items at this stage</div>
    @else
    <div class="pt-wrap">
    <table class="pt-table" wire:key="tbl-{{ $stage }}">
        <thead>
            <tr>
                <th>Item</th>
                <th>Order</th>
                <th>Customer</th>
                <th>Assigned To</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sectionItems as $item)
            @php $active = $item->activeAssignment; @endphp
            <tr class="{{ $item->staff_marked_done ? 'tr-done' : '' }}" wire:key="tr-{{ $item->id }}">

                <td>
                    <span class="pt-iname">{{ $item->description }}</span>
                    @if($item->variant)
                    <span class="pt-variant">{{ $item->variant->variant_value }}</span>
                    @endif
                    @if($item->order?->estimated_completion_date)
                    <span class="pt-ref">📅 {{ $item->order->estimated_completion_date->format('d M') }}</span>
                    @endif
                </td>

                <td><span class="pt-ref" style="font-size:.72rem; margin-top:0;">{{ $item->order?->reference ?? '—' }}</span></td>

                <td><span class="pt-cust">{{ $item->order?->customer_name ?? '—' }}</span></td>

                <td>
                    @if($active?->assignedTo)
                    <span class="pt-assignee">{{ $active->assignedTo->name }}</span>
                    @else
                    <span class="pt-unassigned">Unassigned</span>
                    @endif
                </td>

                <td>
                    @if($item->staff_marked_done)
                    <span class="badge-staff-done">Staff Done ✓</span>
                    @endif
                </td>

                <td>
                    <div class="pt-actions">
                        @if(! $active?->assignedTo)
                        <button disabled class="pt-btn-no-staff" title="Assign a staff member first">⚠ Assign first</button>
                        @elseif(! $item->staff_marked_done)
                        <button disabled class="pt-btn-waiting">⏳ Waiting…</button>
                        @else
                        <button
                            wire:click="advanceStage({{ $item->id }})"
                            wire:loading.attr="disabled"
                            wire:target="advanceStage({{ $item->id }})"
                            class="pt-btn-advance">
                            ✓ Advance →
                        </button>
                        @endif
                        @if($active?->assignedTo)
                        <button wire:click="openAssignModal({{ $item->id }}, '{{ $stage }}')" class="pt-btn-reassign">↺ Reassign</button>
                        @else
                        <button wire:click="openAssignModal({{ $item->id }}, '{{ $stage }}')" class="pt-btn-assign">+ Assign</button>
                        @endif
                        <button wire:click="openDetailsModal({{ $item->id }})" class="pt-btn-details">⋯</button>
                    </div>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @endif

</div>
@endforeach
</div>

{{-- ── Assignment Modal ── --}}
@if($showAssignModal)
@php
    $assignItem  = \App\Models\OrderItem::find($assignItemId);
    $stageLabel  = \App\Models\OrderItem::PRODUCTION_STAGES[$assignStage] ?? $assignStage;
    $stageColour = match($assignStage) {
        'sewing'     => '#6366f1',
        'embroidery' => '#a855f7',
        'printing'   => '#ea580c',
        'finishing'  => '#0891b2',
        default      => '#C9A84C',
    };
    $stageStaff = $this->getStaffForStage($assignStage);
    $hasStaff   = ! empty($stageStaff);
@endphp
<div class="assign-backdrop" wire:click.self="cancelAssign">
    <div class="assign-box">
        <div class="assign-title">
            Assign: <span style="color:{{ $stageColour }};">{{ $stageLabel }}</span>
        </div>
        <div class="assign-sub">{{ $assignItem?->description }}</div>

        <label class="assign-lbl">Assign to</label>
        @if($hasStaff)
        <select wire:model.live="assignStaffId" class="assign-select">
            <option value="">— No assignment —</option>
            @foreach($stageStaff as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        @else
        <select class="assign-select" disabled>
            <option>No staff available for this stage</option>
        </select>
        @endif

        <label class="assign-lbl">Notes <span style="font-weight:400;text-transform:none;">(optional)</span></label>
        <textarea wire:model.live="assignNotes" rows="2" class="assign-notes"
            placeholder="e.g. Use blue thread, rush order…"></textarea>

        <div class="assign-actions">
            <button wire:click="cancelAssign" class="assign-btn assign-cancel">Cancel</button>
            <button wire:click="confirmAssign" class="assign-btn assign-confirm">✓ Confirm</button>
        </div>
    </div>
</div>
@endif

{{-- ── Details Modal ── --}}
@if($showDetailsModal)
@php
    $detailsItem = \App\Models\OrderItem::with([
        'order.customer.measurements.clothingType',
        'variant',
    ])->find($detailsItemId);
    $detailsOrder    = $detailsItem?->order;
    $detailsCustomer = $detailsOrder?->customer;
@endphp
@if($detailsItem)
<div class="details-backdrop" wire:click.self="closeDetailsModal">
<div class="details-box">

    <div class="details-title">{{ $detailsItem->description }}</div>
    <div class="details-sub">
        {{ $detailsOrder?->reference ?? '—' }}
        @if($detailsCustomer) · {{ $detailsCustomer->name }} @endif
        @if($detailsItem->variant)
        · <span style="color:#a855f7;">{{ ucfirst($detailsItem->variant->variant_type) }}: {{ $detailsItem->variant->variant_value }}</span>
        @endif
    </div>

    <hr class="details-divider">

    @if($detailsItem->design_notes)
    <div class="details-section">
        <div class="details-label">Design Notes</div>
        <div class="details-value">{{ $detailsItem->design_notes }}</div>
    </div>
    @endif

    @if($detailsItem->production_notes)
    <div class="details-section">
        <div class="details-label">Production Notes</div>
        <div class="details-value">{{ $detailsItem->production_notes }}</div>
    </div>
    @endif

    @if($detailsItem->design_file)
    @php
        $dfUrl = \Storage::url($detailsItem->design_file);
        $dfExt = strtolower(pathinfo($detailsItem->design_file, PATHINFO_EXTENSION));
        $dfImg = in_array($dfExt, ['jpg','jpeg','png','webp','gif','bmp']);
    @endphp
    <div class="details-section">
        <div class="details-label">Design File</div>
        @if($dfImg)
        <a href="{{ $dfUrl }}" target="_blank" style="display:block;margin-bottom:.4rem;">
            <img src="{{ $dfUrl }}" alt="Design"
                 style="max-width:100%;max-height:240px;object-fit:contain;border-radius:8px;border:1px solid var(--border);">
        </a>
        @endif
        <a href="{{ $dfUrl }}" target="_blank" class="details-file-link">
            📎 {{ basename($detailsItem->design_file) }}
        </a>
    </div>
    @endif

    @if(! $detailsItem->design_notes && ! $detailsItem->production_notes && ! $detailsItem->design_file)
    <div class="details-section">
        <div class="details-value" style="color:var(--muted);font-style:italic;">No design or production notes recorded.</div>
    </div>
    @endif

    @if($detailsCustomer && $detailsCustomer->measurements->isNotEmpty())
    <hr class="details-divider">
    <div class="details-section">
        <div class="details-label">Customer Measurements — {{ $detailsCustomer->name }}</div>
        @foreach($detailsCustomer->measurements as $meas)
        <div style="margin-top:.6rem;">
            <div style="font-size:.72rem;font-weight:700;color:var(--text3);margin-bottom:.3rem;">
                {{ $meas->clothingType?->name ?? 'Unknown Type' }}
                <span style="font-weight:400;color:var(--muted);">({{ $meas->unit }})</span>
            </div>
            <div class="details-meas">
                @foreach($meas->measurements ?? [] as $fieldId => $val)
                @php $field = \App\Models\MeasurementField::find($fieldId); @endphp
                @if($field && $val !== null && $val !== '')
                <div class="details-meas-item"><strong>{{ $field->label }}:</strong> {{ $val }}</div>
                @endif
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <button wire:click="closeDetailsModal" class="details-close">Close</button>

</div>
</div>
@endif
@endif

</x-filament-panels::page>
