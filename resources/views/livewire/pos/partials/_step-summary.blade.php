{{-- ── SUMMARY STEP ─────────────────────────────────────────────────────────── --}}

{{-- Header --}}
<div class="psb-step-hdr">
    <button class="psb-back-btn" wire:click="goTo('order')">←</button>
    <span class="psb-step-lbl">Summary</span>
    <span></span>
</div>

{{-- Body --}}
<div class="psb-scroll">

    {{-- Customer --}}
    <div class="psb-cust-strip">
        <div style="min-width:0;">
            <div style="font-size:13px;font-weight:500;color:#1a1a18;">
                {{ $customerName ?: 'No customer' }}
            </div>
            @if($customerPhone)
                <div style="font-size:10px;color:#9a9690;">{{ $customerPhone }}</div>
            @endif
            @if($this->customer)
                @php $balance = $this->customer->creditBalance(); @endphp
                @if($balance > 0)
                    <div style="font-size:10px;color:#7a3a18;margin-top:2px;">
                        Outstanding: ₦{{ number_format($balance, 0) }}
                    </div>
                @endif
            @endif
        </div>
        <button wire:click="openCustomerModal"
                style="font-size:10px;color:#9a9690;cursor:pointer;border:none;background:none;padding:0;text-decoration:underline;text-underline-offset:3px;flex-shrink:0;">
            edit
        </button>
    </div>

    {{-- Ready date --}}
    @if($estimatedCompletionDate)
        <div class="psb-row">
            <span style="color:#9a9690;">Ready by</span>
            <span style="color:#1a1a18;">{{ \Carbon\Carbon::parse($estimatedCompletionDate)->format('j M Y') }}</span>
        </div>
    @endif

    {{-- Notes --}}
    @if($notes)
        <div class="psb-row" style="flex-direction:column;align-items:flex-start;gap:2px;">
            <span style="color:#9a9690;">Notes</span>
            <span style="font-size:11px;color:#1a1a18;line-height:1.4;">{{ $notes }}</span>
        </div>
    @endif

    {{-- Items with optional per-item delivery toggle --}}
    <div style="padding:4px 0;">
        @foreach($this->cartItems() as $index => $item)
            @php $isDelivery = ($item['delivery_type'] ?? 'pickup') === 'delivery'; @endphp
            <div x-data="{ open: {{ $isDelivery ? 'true' : 'false' }} }"
                 style="padding:10px 18px;border-bottom:1px solid #f0ede8;">

                {{-- Item row --}}
                <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
                    <div style="min-width:0;flex:1;">
                        <div style="font-size:12px;color:#1a1a18;">{{ $item['description'] }}</div>
                        <div style="font-size:10px;color:#9a9690;">
                            ₦{{ number_format((float)($item['unit_price']??0),0) }} × {{ $item['qty'] }}
                            @if($isDelivery)
                                <span style="margin-left:5px;color:#C9A84C;">· Delivery</span>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                        <span style="font-size:12px;color:#1a1a18;">
                            ₦{{ number_format((float)($item['subtotal']??0),0) }}
                        </span>
                        <button @click="open = !open"
                                style="border:none;background:none;cursor:pointer;font-size:14px;color:#c0c0ba;padding:0;line-height:1;letter-spacing:0.05em;"
                                :style="open ? 'color:#1a1a18' : ''">⋯</button>
                    </div>
                </div>

                {{-- Delivery toggle (hidden until ⋯ is clicked) --}}
                <div x-show="open" x-cloak style="display:flex;gap:5px;margin-top:8px;">
                    <button wire:click="updateItem({{ $index }}, 'delivery_type', 'pickup')"
                            style="flex:1;padding:5px 4px;border-radius:5px;font-size:10px;cursor:pointer;transition:all .12s;
                                   {{ !$isDelivery ? 'background:#1a1a18;color:#fff;border:1px solid #1a1a18;font-weight:500;' : 'background:transparent;color:#9a9690;border:1px solid #e0dbd3;' }}">
                        Pickup
                    </button>
                    <button wire:click="updateItem({{ $index }}, 'delivery_type', 'delivery')"
                            style="flex:1;padding:5px 4px;border-radius:5px;font-size:10px;cursor:pointer;transition:all .12s;
                                   {{ $isDelivery ? 'background:#1a1a18;color:#fff;border:1px solid #1a1a18;font-weight:500;' : 'background:transparent;color:#9a9690;border:1px solid #e0dbd3;' }}">
                        Deliver
                    </button>
                </div>

                @if($isDelivery && $customerAddress)
                    <div style="font-size:10px;color:#9a9690;margin-top:5px;">→ {{ $customerAddress }}</div>
                @endif

            </div>
        @endforeach
    </div>

</div>

{{-- Footer --}}
<div class="psb-foot">
    <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:10px;">
        <span style="font-size:11px;color:#9a9690;">Total</span>
        <span class="psb-serif" style="font-size:24px;font-weight:500;color:#1a1a18;letter-spacing:-0.01em;line-height:1;">
            ₦{{ number_format($this->subtotal(),0) }}
        </span>
    </div>
    <button class="psb-gold-btn" wire:click="proceedToPayment">
        Proceed to Payment →
    </button>
</div>
