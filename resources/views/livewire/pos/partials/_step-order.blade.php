{{-- ── ORDER STEP ────────────────────────────────────────────────────────────── --}}

{{-- Header --}}
<div class="psb-hdr">
    <span style="font-size:9px;letter-spacing:0.13em;color:#9a9690;text-transform:uppercase;">New Order</span>
</div>

{{-- Body --}}
<div class="psb-scroll" style="display:flex;flex-direction:column;">

    {{-- Cart items (empty by default) --}}
    @foreach($this->cartItems() as $index => $item)
        <div class="psb-item" wire:key="si-{{ $index }}">

            {{-- Name: editable input for manual lines, plain text for catalogue items --}}
            @if(($item['product_id'] ?? null) === null)
                <input type="text"
                       value="{{ $item['description'] }}"
                       placeholder="Item description…"
                       @change="$wire.updateItem({{ $index }}, 'description', $event.target.value)"
                       style="flex:1;min-width:0;border:none;border-bottom:1px solid #e0dbd3;padding:2px 0;font-size:12px;color:#1a1a18;background:transparent;outline:none;" />
            @else
                <div style="flex:1;min-width:0;font-size:12px;color:#1a1a18;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $item['description'] }}
                    @if(($item['production_type']??'ready_made')==='production')
                        <span style="font-size:9px;background:#f5f0e8;color:#92400e;padding:1px 4px;border-radius:2px;font-weight:500;letter-spacing:0.04em;margin-left:4px;">BESPOKE</span>
                    @endif
                </div>
            @endif

            {{-- Qty controls --}}
            <div style="display:flex;align-items:center;gap:5px;flex-shrink:0;">
                <button class="psb-qty-btn" wire:click="decrementQty({{ $index }})">−</button>
                <span style="font-size:12px;min-width:14px;text-align:center;color:#1a1a18;">{{ $item['qty'] }}</span>
                <button class="psb-qty-btn" wire:click="incrementQty({{ $index }})">+</button>
            </div>

            {{-- Price: editable for manual lines --}}
            @if(($item['product_id'] ?? null) === null)
                <div style="display:flex;align-items:baseline;gap:1px;flex-shrink:0;">
                    <span style="font-size:10px;color:#9a9690;">₦</span>
                    <input type="number" min="0"
                           value="{{ $item['unit_price'] ?? 0 }}"
                           placeholder="0"
                           @change="$wire.updateItem({{ $index }}, 'unit_price', parseFloat($event.target.value) || 0)"
                           style="border:none;border-bottom:1px solid #e0dbd3;padding:2px 0;font-size:12px;color:#1a1a18;background:transparent;outline:none;width:58px;text-align:right;" />
                </div>
            @else
                <span style="font-size:12px;min-width:58px;text-align:right;color:#1a1a18;flex-shrink:0;">
                    ₦{{ number_format((float)($item['subtotal']??0),0) }}
                </span>
            @endif

            {{-- Remove --}}
            <button wire:click="removeItem({{ $index }})"
                    style="border:none;background:none;cursor:pointer;color:#ccc;padding:0;font-size:15px;line-height:1;flex-shrink:0;">×</button>
        </div>
    @endforeach

    {{-- Spacer pushes customer/fulfillment to bottom --}}
    <div style="flex:1;min-height:12px;"></div>

    {{-- Divider --}}
    <div style="height:1px;background:#f0ede8;"></div>

    {{-- Customer --}}
    <div style="padding:14px 18px 10px;">
        <div style="font-size:9px;color:#9a9690;letter-spacing:0.1em;margin-bottom:8px;">CUSTOMER</div>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
            @if($customerName)
                <div>
                    <div style="font-size:13px;color:#1a1a18;font-weight:500;">{{ $customerName }}</div>
                    @if($customerPhone)
                        <div style="font-size:10px;color:#9a9690;margin-top:1px;">{{ $customerPhone }}</div>
                    @endif
                </div>
            @else
                <div style="font-size:12px;color:#b0aca6;">Walk-in customer</div>
            @endif
            <button wire:click="openCustomerModal"
                    style="font-size:10px;color:#9a9690;cursor:pointer;border:none;background:none;padding:0;text-decoration:underline;text-underline-offset:3px;flex-shrink:0;">
                {{ $customerName ? 'Change' : '+ Add' }}
            </button>
        </div>
    </div>


</div>

{{-- Footer --}}
<div class="psb-foot">
    <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:10px;">
        <span style="font-size:11px;color:#9a9690;">Subtotal</span>
        <span class="psb-serif" style="font-size:24px;font-weight:500;color:#1a1a18;letter-spacing:-0.01em;line-height:1;">
            ₦{{ number_format($this->subtotal(),0) }}
        </span>
    </div>
    <div style="display:flex;gap:7px;">
        <button class="psb-ghost-btn" wire:click="clearCart">Clear</button>
        <button class="psb-gold-btn" style="flex:1;width:auto;"
                wire:click="goTo('summary')"
                @if(empty($this->cartItems())) disabled @endif>
            Process →
        </button>
    </div>
</div>
