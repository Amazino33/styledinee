@once
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&display=swap" rel="stylesheet">
<style>
/* ── POS Sidebar ─────────────────────────────────────────────────────────── */
.psb                    { display:flex; flex-direction:column; height:100%; font-family:'DM Sans',sans-serif; background:#fff; }
.dark .psb              { background:#1f2937; }
.psb-hdr                { padding:13px 18px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #ece9e3; flex-shrink:0; }
.dark .psb-hdr          { border-bottom-color:#374151; }
.psb-step-hdr           { padding:13px 18px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #ece9e3; min-height:48px; position:relative; flex-shrink:0; }
.dark .psb-step-hdr     { border-bottom-color:#374151; }
.psb-step-lbl           { font-size:9px; letter-spacing:0.13em; color:#9a9690; text-transform:uppercase; position:absolute; left:50%; transform:translateX(-50%); white-space:nowrap; }
.dark .psb-step-lbl     { color:#6b7280; }
.psb-scroll             { flex:1; overflow-y:auto; min-height:0; }
.psb-foot               { padding:12px 18px 18px; border-top:1px solid #ece9e3; flex-shrink:0; }
.dark .psb-foot         { border-top-color:#374151; }
.psb-gold-btn           { width:100%; padding:12px; background:#C9A84C; border:none; border-radius:8px; font-size:13px; font-weight:500; color:#1a1a18; cursor:pointer; letter-spacing:0.02em; transition:opacity .15s; }
.psb-gold-btn:disabled  { opacity:.4; cursor:not-allowed; }
.psb-ghost-btn          { padding:12px 16px; background:none; border:1px solid #ece9e3; border-radius:8px; font-size:12px; color:#9a9690; cursor:pointer; white-space:nowrap; transition:border-color .15s; }
.dark .psb-ghost-btn    { border-color:#374151; color:#6b7280; }
.psb-back-btn           { border:none; background:none; cursor:pointer; font-size:18px; color:#1a1a18; padding:0; line-height:1; }
.dark .psb-back-btn     { color:#f9fafb; }
.psb-tab-bar            { display:flex; border-bottom:1px solid #ece9e3; flex-shrink:0; }
.dark .psb-tab-bar      { border-bottom-color:#374151; }
.psb-tab                { flex:1; padding:9px 0; border:none; border-bottom:2px solid transparent; background:none; font-size:11px; color:#9a9690; cursor:pointer; transition:all .15s; }
.psb-tab.active         { border-bottom-color:#1a1a18; color:#1a1a18; font-weight:500; }
.dark .psb-tab.active   { border-bottom-color:#f9fafb; color:#f9fafb; }
.psb-item               { display:flex; align-items:center; padding:10px 18px; gap:8px; border-bottom:1px solid #f0ede8; }
.dark .psb-item         { border-bottom-color:#374151; }
.psb-item:last-child    { border-bottom:none; }
.psb-qty-btn            { width:20px; height:20px; border-radius:50%; border:1px solid #d8d4cd; background:none; cursor:pointer; font-size:12px; color:#7a7671; display:flex; align-items:center; justify-content:center; padding:0; flex-shrink:0; line-height:1; transition:border-color .12s; }
.dark .psb-qty-btn      { border-color:#4b5563; color:#9ca3af; }
.psb-seg-bar            { display:flex; background:#f5f3ef; border-radius:8px; padding:3px; gap:2px; }
.dark .psb-seg-bar      { background:#1a2535; }
.psb-seg-btn            { flex:1; padding:7px 4px; border:none; border-radius:6px; font-size:11px; cursor:pointer; background:transparent; color:#9a9690; transition:all .15s; }
.psb-seg-btn.active     { background:#fff; color:#1a1a18; font-weight:500; border:0.5px solid #e0dbd3; box-shadow:0 0.5px 2px rgba(0,0,0,0.08); }
.dark .psb-seg-btn.active { background:#1f2937; color:#f9fafb; border-color:#374151; }
.psb-amt-lbl            { font-size:9px; color:#9a9690; letter-spacing:0.1em; margin-bottom:6px; }
.psb-amt-row            { display:flex; align-items:baseline; gap:5px; }
.psb-amt-sym            { font-size:16px; color:#9a9690; font-family:'Cormorant Garamond',Georgia,serif; }
.psb-amt-input          { flex:1; border:none; border-bottom:1px solid #e0dbd3; padding:5px 0; font-size:22px; font-weight:500; color:#1a1a18; background:transparent; outline:none; width:100%; font-family:'Cormorant Garamond',Georgia,serif; }
.dark .psb-amt-input    { color:#f9fafb; border-bottom-color:#4b5563; }
.psb-info-card          { border-radius:8px; padding:11px 13px; }
.psb-serif              { font-family:'Cormorant Garamond',Georgia,serif; }
.psb-coupon-row         { display:flex; align-items:center; gap:8px; }
.psb-coupon-input       { flex:1; border:none; border-bottom:1px solid #e0dbd3; padding:5px 0; font-size:12px; color:#1a1a18; background:transparent; outline:none; text-transform:uppercase; letter-spacing:0.05em; }
.dark .psb-coupon-input { color:#f9fafb; border-bottom-color:#4b5563; }
.psb-coupon-input.error { border-bottom-color:#e0b0a0; }
.psb-cust-strip         { padding:12px 18px; border-bottom:1px solid #f0ede8; display:flex; align-items:center; justify-content:space-between; gap:8px; }
.dark .psb-cust-strip   { border-bottom-color:#374151; }
.psb-row                { display:flex; justify-content:space-between; align-items:center; padding:9px 18px; border-bottom:1px solid #f0ede8; font-size:11px; }
.dark .psb-row          { border-bottom-color:#374151; }
</style>
@endpush
@endonce

<div class="psb" wire:key="order-sidebar">
    @if($step === 'order')
        @include('livewire.pos.partials._step-order')
    @elseif($step === 'summary')
        @include('livewire.pos.partials._step-summary')
    @elseif($step === 'payment')
        @include('livewire.pos.partials._step-payment')
    @else
        @include('livewire.pos.partials._step-done')
    @endif
</div>
