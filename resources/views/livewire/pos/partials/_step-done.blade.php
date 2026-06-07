{{-- ── DONE STEP ────────────────────────────────────────────────────────────── --}}

<div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;padding:24px;">
    <div style="width:44px;height:44px;border-radius:50%;background:#edf7f1;display:flex;align-items:center;justify-content:center;font-size:22px;color:#2a7a4b;">✓</div>
    <div style="font-size:15px;font-weight:500;color:#1a1a18;margin-top:4px;">Order complete</div>
    <div style="font-size:12px;color:#9a9690;text-align:center;">
        ₦{{ number_format($this->total,0) }} received
    </div>
    <div style="font-size:11px;color:#9a9690;text-align:center;margin-top:4px;">
        Check the receipt above to print or share.
    </div>
    <button class="psb-gold-btn" style="width:auto;padding:10px 28px;margin-top:16px;"
            wire:click="newSale">
        + New Order
    </button>
</div>
