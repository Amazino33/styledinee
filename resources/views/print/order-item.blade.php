<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $order->reference }} — {{ $item->description }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #111;
            background: #fff;
            padding: 24px;
            max-width: 720px;
            margin: 0 auto;
        }

        /* ── Screen-only controls ── */
        .screen-only {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .btn-print {
            padding: 8px 20px;
            background: #C9A84C;
            color: #111;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-close {
            padding: 8px 20px;
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }

        /* ── Header ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #111;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .brand { font-size: 18px; font-weight: 800; letter-spacing: .02em; }
        .brand span { color: #C9A84C; }
        .ref-block { text-align: right; }
        .ref { font-size: 15px; font-weight: 700; }
        .date { font-size: 11px; color: #6b7280; margin-top: 2px; }

        /* ── Section ── */
        .section { margin-bottom: 16px; }
        .section-title {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 1px solid #e5e7eb;
        }

        /* ── Item info ── */
        .item-name { font-size: 17px; font-weight: 700; margin-bottom: 4px; }
        .item-meta { font-size: 11px; color: #6b7280; display: flex; gap: 12px; flex-wrap: wrap; }
        .stage-chip {
            display: inline-block;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 2px 7px;
            border-radius: 4px;
            background: rgba(168,85,247,.12);
            color: #7c3aed;
            border: 1px solid rgba(168,85,247,.25);
        }
        .stage-chip.sewing     { background: rgba(99,102,241,.1); color: #4338ca; border-color: rgba(99,102,241,.25); }
        .stage-chip.printing   { background: rgba(59,130,246,.1); color: #1d4ed8; border-color: rgba(59,130,246,.25); }
        .stage-chip.finishing  { background: rgba(245,158,11,.1); color: #b45309; border-color: rgba(245,158,11,.25); }

        /* ── Customer row ── */
        .customer-row { display: flex; gap: 32px; flex-wrap: wrap; }
        .field-pair { display: flex; flex-direction: column; gap: 1px; }
        .field-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #9ca3af; }
        .field-value { font-size: 13px; font-weight: 600; }

        /* ── Measurements ── */
        .meas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 6px 16px;
        }
        .meas-item { font-size: 12px; }
        .meas-item strong { font-weight: 700; }

        /* ── Design notes ── */
        .notes-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 13px;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        /* ── Design file ── */
        .design-img {
            max-width: 100%;
            max-height: 320px;
            object-fit: contain;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            display: block;
            margin-bottom: 6px;
        }
        .file-link { font-size: 12px; color: #4f46e5; }

        /* ── Signature line ── */
        .sig-row {
            display: flex;
            gap: 40px;
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }
        .sig-box { flex: 1; }
        .sig-line { border-bottom: 1px solid #9ca3af; height: 32px; margin-bottom: 4px; }
        .sig-name { font-size: 10px; color: #9ca3af; }

        /* ── Print overrides ── */
        @media print {
            .screen-only { display: none !important; }
            body { padding: 12px; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>

<div class="screen-only">
    <button class="btn-print" onclick="window.print()">🖨 Print</button>
    <button class="btn-close" onclick="window.close()">✕ Close</button>
</div>

{{-- ── Header ── --}}
<div class="header">
    <div class="brand">Styled<span>inee</span></div>
    <div class="ref-block">
        <div class="ref">{{ $order->reference }}</div>
        <div class="date">Printed {{ now()->format('d M Y, g:ia') }}</div>
    </div>
</div>

{{-- ── Item ── --}}
<div class="section">
    <div class="item-name">{{ $item->description }}</div>
    <div class="item-meta">
        @php
            $stage = $item->item_stage;
            $stageLabel = \App\Models\OrderItem::PRODUCTION_STAGES[$stage] ?? ucfirst($stage);
        @endphp
        <span class="stage-chip {{ $stage }}">{{ $stageLabel }}</span>
        @if($item->variant)
        <span>{{ ucfirst($item->variant->variant_type) }}: {{ $item->variant->variant_value }}</span>
        @endif
        @if($item->quantity > 1)
        <span>Qty: {{ $item->quantity }}</span>
        @endif
    </div>
</div>

{{-- ── Customer ── --}}
<div class="section">
    <div class="section-title">Customer</div>
    <div class="customer-row">
        <div class="field-pair">
            <span class="field-label">Name</span>
            <span class="field-value">{{ $order->customer_name }}</span>
        </div>
        @if($order->customer_phone)
        <div class="field-pair">
            <span class="field-label">Phone</span>
            <span class="field-value">{{ $order->customer_phone }}</span>
        </div>
        @endif
    </div>
</div>

{{-- ── Measurements ── --}}
@if($measurements->isNotEmpty())
<div class="section">
    <div class="section-title">Measurements</div>
    <div class="meas-grid">
        @foreach($measurements as $m)
        <div class="meas-item"><strong>{{ $m['label'] }}:</strong> {{ $m['value'] }}</div>
        @endforeach
    </div>
</div>
@endif

{{-- ── Design Notes ── --}}
@if($item->design_notes)
<div class="section">
    <div class="section-title">Design Notes</div>
    <div class="notes-box">{{ $item->design_notes }}</div>
</div>
@endif

{{-- ── Production Notes ── --}}
@if($item->production_notes)
<div class="section">
    <div class="section-title">Production Notes</div>
    <div class="notes-box">{{ $item->production_notes }}</div>
</div>
@endif

{{-- ── Design File ── --}}
@if($item->design_file)
@php
    $dfUrl = \Storage::url($item->design_file);
    $dfExt = strtolower(pathinfo($item->design_file, PATHINFO_EXTENSION));
    $dfImg = in_array($dfExt, ['jpg','jpeg','png','webp','gif','bmp']);
@endphp
<div class="section">
    <div class="section-title">Design File</div>
    @if($dfImg)
    <img src="{{ $dfUrl }}" alt="Design" class="design-img">
    @endif
    <span class="file-link">📎 {{ basename($item->design_file) }}</span>
</div>
@endif

{{-- ── Signature lines ── --}}
<div class="sig-row">
    <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-name">Staff Signature</div>
    </div>
    <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-name">Date Completed</div>
    </div>
    <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-name">Supervisor Check</div>
    </div>
</div>

<script>
    window.addEventListener('load', () => window.print());
</script>
</body>
</html>
