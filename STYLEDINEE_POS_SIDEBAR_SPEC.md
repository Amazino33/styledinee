# Styledinee POS — Order Sidebar Implementation

## Task
Replace the current right-panel sidebar on the POS page (`/admin/pos`) with a new
minimalist multi-step sidebar. The design reference is `StyledineeOrderSidebar.jsx`
in this repo. Do NOT copy that file — it is a React mockup used for design reference
only. The real implementation is Livewire + Alpine.js + Blade.

---

## Architecture

Use a **hybrid Livewire + Alpine** pattern:

| Concern | Tool |
|---|---|
| Cart state (items, qty, delivery date, coupon) | Livewire properties |
| Step navigation (order → summary → payment → done) | Livewire `$step` |
| Adding products from the left-panel grid | Livewire event listener |
| Payment math (cash, transfer, split, change) | Alpine.js (client-side only — no round-trips) |
| Coupon validation | Livewire method (hits DB) |
| Order completion | Livewire method (writes DB) |

---

## Files to Create

```
app/Livewire/Pos/OrderSidebar.php
resources/views/livewire/pos/order-sidebar.blade.php
resources/views/livewire/pos/partials/
    _step-order.blade.php
    _step-summary.blade.php
    _step-payment.blade.php
    _step-done.blade.php
```

## File to Modify

```
resources/views/filament/pages/pos.blade.php
```
Replace the existing right-panel markup with:
```blade
<livewire:pos.order-sidebar />
```

---

## 1. Livewire Component — `app/Livewire/Pos/OrderSidebar.php`

```php
<?php

namespace App\Livewire\Pos;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Livewire\Attributes\Computed;
use Livewire\Component;

class OrderSidebar extends Component
{
    // ── Cart ──────────────────────────────────────────────────
    /** @var array<int, array{id:int, name:string, qty:int, unitPrice:int}> */
    public array $cartItems = [];

    public string $deliveryDate = '';

    // ── Customer ─────────────────────────────────────────────
    public ?int $customerId = null;
    // Populated when customer is selected via the customer modal on the POS page.
    // The parent POS page dispatches 'customer-selected' with ['customerId' => $id].

    // ── Step ─────────────────────────────────────────────────
    /** order | summary | payment | done */
    public string $step = 'order';

    // ── Coupon ───────────────────────────────────────────────
    public string $couponInput  = '';
    public string $couponCode   = '';   // applied code
    public int    $couponPct    = 0;    // e.g. 10 for 10%
    public string $couponLabel  = '';   // e.g. "10% off"
    public string $couponError  = '';

    // ── Listeners ────────────────────────────────────────────
    protected $listeners = [
        'product-clicked'   => 'addProduct',
        'customer-selected' => 'setCustomer',
    ];

    // ── Computed ─────────────────────────────────────────────
    #[Computed]
    public function subtotal(): int
    {
        return collect($this->cartItems)->sum(fn($i) => $i['qty'] * $i['unitPrice']);
    }

    #[Computed]
    public function discountAmount(): int
    {
        return $this->couponPct > 0
            ? (int) round($this->subtotal * $this->couponPct / 100)
            : 0;
    }

    #[Computed]
    public function total(): int
    {
        return $this->subtotal - $this->discountAmount;
    }

    #[Computed]
    public function customer(): ?Customer
    {
        return $this->customerId ? Customer::find($this->customerId) : null;
    }

    // ── Cart actions ─────────────────────────────────────────
    public function addProduct(int $productId): void
    {
        $existing = collect($this->cartItems)->search(fn($i) => $i['id'] === $productId);

        if ($existing !== false) {
            $this->cartItems[$existing]['qty']++;
        } else {
            $product = \App\Models\Product::find($productId);
            if (!$product) return;

            $this->cartItems[] = [
                'id'        => $product->id,
                'name'      => $product->name,
                'qty'       => 1,
                'unitPrice' => (int) $product->price,
            ];
        }
    }

    public function changeQty(int $index, int $delta): void
    {
        if (!isset($this->cartItems[$index])) return;

        $newQty = $this->cartItems[$index]['qty'] + $delta;

        if ($newQty < 1) {
            $this->removeItem($index);
            return;
        }

        $this->cartItems[$index]['qty'] = $newQty;
    }

    public function removeItem(int $index): void
    {
        array_splice($this->cartItems, $index, 1);
        $this->cartItems = array_values($this->cartItems);
    }

    public function clearCart(): void
    {
        $this->cartItems    = [];
        $this->deliveryDate = '';
        $this->removeCoupon();
    }

    // ── Customer ─────────────────────────────────────────────
    public function setCustomer(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    // ── Coupon ───────────────────────────────────────────────
    public function applyCoupon(): void
    {
        $this->couponError = '';
        $code = strtoupper(trim($this->couponInput));

        // Look up coupon in DB. Adjust to your actual Coupon model/fields.
        $coupon = Coupon::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            $this->couponError = 'Invalid code';
            return;
        }

        $this->couponCode  = $coupon->code;
        $this->couponPct   = $coupon->discount_percentage;  // integer, e.g. 10
        $this->couponLabel = $coupon->discount_percentage . '% off';
        $this->couponInput = '';
    }

    public function removeCoupon(): void
    {
        $this->couponCode  = '';
        $this->couponPct   = 0;
        $this->couponLabel = '';
        $this->couponInput = '';
        $this->couponError = '';
    }

    // ── Navigation ───────────────────────────────────────────
    public function goTo(string $step): void
    {
        $this->step = $step;
    }

    // ── Complete order ───────────────────────────────────────
    /**
     * Called from Alpine on the payment step after client-side validation.
     * Receives final payment breakdown from the client.
     *
     * @param string $payMode   cash|transfer|split
     * @param int    $cashAmt   amount in kobo or naira — match your convention
     * @param int    $transferAmt
     */
    public function completeOrder(string $payMode, int $cashAmt, int $transferAmt): void
    {
        // 1. Create the order
        $order = Order::create([
            'customer_id'   => $this->customerId,
            'delivery_date' => $this->deliveryDate ?: null,
            'coupon_code'   => $this->couponCode ?: null,
            'subtotal'      => $this->subtotal,
            'discount'      => $this->discountAmount,
            'total'         => $this->total,
            'status'        => 'pending',
        ]);

        // 2. Create order items
        foreach ($this->cartItems as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['id'],
                'name'       => $item['name'],
                'qty'        => $item['qty'],
                'unit_price' => $item['unitPrice'],
                'subtotal'   => $item['qty'] * $item['unitPrice'],
            ]);
        }

        // 3. Record payment
        Payment::create([
            'order_id'     => $order->id,
            'pay_mode'     => $payMode,
            'cash_amount'  => $cashAmt,
            'transfer_amount' => $transferAmt,
            'total'        => $this->total,
        ]);

        // 4. Move to done step
        $this->step = 'done';
    }

    public function resetAll(): void
    {
        $this->cartItems    = [];
        $this->deliveryDate = '';
        $this->step         = 'order';
        $this->customerId   = null;
        $this->removeCoupon();
    }

    public function render()
    {
        return view('livewire.pos.order-sidebar');
    }
}
```

---

## 2. Main Blade View — `resources/views/livewire/pos/order-sidebar.blade.php`

```blade
{{-- Load fonts --}}
@once
    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&display=swap" rel="stylesheet">
        <style>
            .pos-sidebar               { width: 300px; background: #fff; border-left: 1px solid #ece9e3; display: flex; flex-direction: column; height: 100vh; font-family: 'DM Sans', sans-serif; }
            .pos-sidebar-header        { padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ece9e3; }
            .pos-sidebar-footer        { padding: 14px 20px 22px; border-top: 1px solid #ece9e3; }
            .pos-step-header           { padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #ece9e3; min-height: 52px; position: relative; }
            .pos-step-label            { font-size: 10px; letter-spacing: 0.12em; color: #9a9690; text-transform: uppercase; position: absolute; left: 50%; transform: translateX(-50%); }
            .pos-scrollable            { flex: 1; overflow-y: auto; }
            .pos-avatar                { width: 26px; height: 26px; border-radius: 50%; background: #1a1a18; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #C9A84C; font-weight: 600; flex-shrink: 0; }
            .pos-item-row              { display: flex; align-items: center; padding: 11px 20px; gap: 10px; border-bottom: 1px solid #f0ede8; }
            .pos-item-row:last-child   { border-bottom: none; }
            .pos-qty-btn               { width: 22px; height: 22px; border-radius: 50%; border: 1px solid #d8d4cd; background: none; cursor: pointer; font-size: 13px; color: #7a7671; display: flex; align-items: center; justify-content: center; line-height: 1; padding: 0; flex-shrink: 0; }
            .pos-gold-btn              { width: 100%; padding: 13px; background: #C9A84C; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; color: #1a1a18; cursor: pointer; letter-spacing: 0.02em; }
            .pos-ghost-btn             { padding: 13px 18px; background: none; border: 1px solid #ece9e3; border-radius: 8px; font-size: 13px; color: #9a9690; cursor: pointer; white-space: nowrap; }
            .pos-tab-bar               { display: flex; border-bottom: 1px solid #ece9e3; }
            .pos-tab                   { flex: 1; padding: 10px 0; border: none; border-bottom: 2px solid transparent; background: none; font-size: 12px; color: #9a9690; cursor: pointer; transition: color 0.15s; }
            .pos-tab.active            { border-bottom-color: #1a1a18; color: #1a1a18; font-weight: 500; }
            .pos-seg-bar               { display: flex; background: #f5f3ef; border-radius: 8px; padding: 3px; gap: 2px; }
            .pos-seg-btn               { flex: 1; padding: 7px 4px; border: none; border-radius: 6px; font-size: 12px; cursor: pointer; background: transparent; color: #9a9690; transition: all 0.15s; }
            .pos-seg-btn.active        { background: #fff; color: #1a1a18; font-weight: 500; border: 0.5px solid #e0dbd3; box-shadow: 0 0.5px 2px rgba(0,0,0,0.08); }
            .pos-amt-label             { font-size: 10px; color: #9a9690; letter-spacing: 0.1em; margin-bottom: 8px; }
            .pos-amt-row               { display: flex; align-items: baseline; gap: 6px; }
            .pos-amt-sym               { font-size: 18px; color: #9a9690; font-family: 'Cormorant Garamond', Georgia, serif; }
            .pos-amt-input             { flex: 1; border: none; border-bottom: 1px solid #e0dbd3; padding: 6px 0; font-size: 22px; font-weight: 500; color: #1a1a18; background: transparent; outline: none; width: 100%; font-family: 'Cormorant Garamond', Georgia, serif; }
            .pos-info-card             { border-radius: 8px; padding: 12px 14px; }
            .pos-serif                 { font-family: 'Cormorant Garamond', Georgia, serif; }
            .pos-affiliate-tag         { font-size: 10px; color: #9a9690; background: #f5f3ef; padding: 1px 5px; border-radius: 4px; }
            .pos-coupon-input          { flex: 1; border: none; border-bottom: 1px solid #e0dbd3; padding: 6px 0; font-size: 13px; color: #1a1a18; background: transparent; outline: none; text-transform: uppercase; }
            .pos-coupon-input.error    { border-bottom-color: #e0b0a0; }
        </style>
    @endpush
@endonce

<div class="pos-sidebar">
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
```

---

## 3. Step Partials

### `_step-order.blade.php`

```blade
{{-- Header --}}
<div class="pos-sidebar-header">
    <span style="font-size:10px;letter-spacing:0.12em;color:#9a9690;text-transform:uppercase;">Order</span>
    <div style="display:flex;align-items:center;gap:8px;">
        <div class="pos-avatar">{{ $this->customer ? strtoupper(substr($this->customer->first_name,0,1).substr($this->customer->last_name,0,1)) : '?' }}</div>
        <div>
            <div style="display:flex;align-items:center;gap:5px;">
                <span style="font-size:13px;color:#1a1a18;">{{ $this->customer?->full_name ?? 'No customer' }}</span>
                @if($this->customer?->affiliate)
                    <span class="pos-affiliate-tag">via {{ $this->customer->affiliate->name }}</span>
                @endif
            </div>
        </div>
        <span style="font-size:11px;color:#9a9690;cursor:pointer;text-decoration:underline;text-underline-offset:3px;"
              x-on:click="$dispatch('open-customer-modal')">edit</span>
    </div>
</div>

{{-- Tabs (Alpine-driven, no Livewire round-trip) --}}
<div x-data="{ tab: 'items' }">
    <div class="pos-tab-bar">
        <button class="pos-tab" :class="{ active: tab === 'items' }" @click="tab = 'items'">Items</button>
        <button class="pos-tab" :class="{ active: tab === 'delivery' }" @click="tab = 'delivery'">
            Delivery
            @if($deliveryDate)
                <span style="color:#C9A84C;margin-left:3px;">●</span>
            @endif
        </button>
    </div>

    {{-- Items tab --}}
    <div x-show="tab === 'items'" class="pos-scrollable" style="padding-top:4px;">
        @forelse($cartItems as $index => $item)
            <div class="pos-item-row">
                <span style="flex:1;font-size:13px;color:#1a1a18;">{{ $item['name'] }}</span>
                <div style="display:flex;align-items:center;gap:8px;">
                    <button class="pos-qty-btn" wire:click="changeQty({{ $index }}, -1)">−</button>
                    <span style="font-size:13px;min-width:16px;text-align:center;color:#1a1a18;">{{ $item['qty'] }}</span>
                    <button class="pos-qty-btn" wire:click="changeQty({{ $index }}, 1)">+</button>
                </div>
                <span style="font-size:13px;min-width:62px;text-align:right;color:#1a1a18;">
                    ₦{{ number_format($item['qty'] * $item['unitPrice']) }}
                </span>
                <button wire:click="removeItem({{ $index }})"
                        style="border:none;background:none;cursor:pointer;color:#ccc;padding:0;font-size:16px;line-height:1;flex-shrink:0;">×</button>
            </div>
        @empty
            <div style="padding:40px 20px;text-align:center;color:#b0aca6;font-size:13px;">No items yet</div>
        @endforelse

        <div style="padding:10px 20px;">
            <button style="border:none;background:none;cursor:pointer;font-size:12px;color:#9a9690;padding:0;">+ Add line</button>
        </div>
    </div>

    {{-- Delivery tab --}}
    <div x-show="tab === 'delivery'" class="pos-scrollable" style="padding:24px 20px;display:flex;flex-direction:column;gap:6px;">
        <div style="font-size:11px;color:#9a9690;letter-spacing:0.08em;margin-bottom:10px;">EXPECTED DELIVERY DATE</div>
        <input type="date"
               wire:model.live="deliveryDate"
               style="border:none;border-bottom:1px solid #e0dbd3;padding:8px 0;font-size:15px;color:#1a1a18;background:transparent;outline:none;width:100%;font-family:inherit;" />
        @if($deliveryDate)
            <div style="margin-top:14px;font-size:13px;color:#1a1a18;font-family:'Cormorant Garamond',Georgia,serif;letter-spacing:0.01em;">
                {{ \Carbon\Carbon::parse($deliveryDate)->format('l, j F Y') }}
            </div>
        @endif
    </div>
</div>

{{-- Footer --}}
<div class="pos-sidebar-footer">
    <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:14px;">
        <span style="font-size:12px;color:#9a9690;">Total</span>
        <span class="pos-serif" style="font-size:26px;font-weight:500;color:#1a1a18;letter-spacing:-0.01em;line-height:1;">
            ₦{{ number_format($this->subtotal) }}
        </span>
    </div>
    <div style="display:flex;gap:8px;">
        <button class="pos-ghost-btn" wire:click="clearCart">Clear</button>
        <button class="pos-gold-btn" style="flex:1;width:auto;"
                wire:click="goTo('summary')"
                @if(empty($cartItems)) disabled style="opacity:0.45;cursor:not-allowed;" @endif>
            Process →
        </button>
    </div>
</div>
```

---

### `_step-summary.blade.php`

```blade
{{-- Header --}}
<div class="pos-step-header">
    <button wire:click="goTo('order')" style="border:none;background:none;cursor:pointer;font-size:20px;color:#1a1a18;padding:0;line-height:1;">←</button>
    <span class="pos-step-label">Summary</span>
    <span></span>
</div>

{{-- Body --}}
<div class="pos-scrollable">
    {{-- Customer --}}
    <div style="padding:14px 20px;border-bottom:1px solid #ece9e3;display:flex;align-items:center;gap:10px;">
        <div class="pos-avatar">{{ strtoupper(substr($this->customer?->first_name ?? '?',0,1).substr($this->customer?->last_name ?? '',0,1)) }}</div>
        <div>
            <div style="display:flex;align-items:center;gap:6px;">
                <span style="font-size:13px;font-weight:500;color:#1a1a18;">{{ $this->customer?->full_name }}</span>
                @if($this->customer?->affiliate)
                    <span class="pos-affiliate-tag">via {{ $this->customer->affiliate->name }}</span>
                @endif
            </div>
            <span style="font-size:11px;color:#9a9690;">{{ $this->customer?->phone }}</span>
        </div>
    </div>

    {{-- Delivery date --}}
    @if($deliveryDate)
        <div style="padding:10px 20px;border-bottom:1px solid #f0ede8;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:12px;color:#9a9690;">Delivery date</span>
            <span style="font-size:12px;color:#1a1a18;">{{ \Carbon\Carbon::parse($deliveryDate)->format('j M Y') }}</span>
        </div>
    @endif

    {{-- Items --}}
    <div style="padding:6px 0;">
        @foreach($cartItems as $item)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 20px;border-bottom:1px solid #f0ede8;">
                <div>
                    <div style="font-size:13px;color:#1a1a18;">{{ $item['name'] }}</div>
                    <div style="font-size:11px;color:#9a9690;">₦{{ number_format($item['unitPrice']) }} × {{ $item['qty'] }}</div>
                </div>
                <span style="font-size:13px;color:#1a1a18;">₦{{ number_format($item['qty'] * $item['unitPrice']) }}</span>
            </div>
        @endforeach
    </div>
</div>

{{-- Footer --}}
<div class="pos-sidebar-footer">
    <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:14px;">
        <span style="font-size:12px;color:#9a9690;">Total</span>
        <span class="pos-serif" style="font-size:26px;font-weight:500;color:#1a1a18;letter-spacing:-0.01em;line-height:1;">
            ₦{{ number_format($this->subtotal) }}
        </span>
    </div>
    <button class="pos-gold-btn" wire:click="goTo('payment')">Proceed to Payment →</button>
</div>
```

---

### `_step-payment.blade.php`

> **Important:** All payment math is Alpine-only. `completeOrder()` is called via
> `$wire.completeOrder(payMode, cashAmt, transferAmt)` from Alpine — this avoids
> Livewire latency during cash entry.

```blade
<div
    x-data="{
        payMode: 'cash',
        cashAmt: '',
        transferAmt: '',
        couponApplied: {{ $couponCode ? 'true' : 'false' }},
        subtotal: {{ $this->subtotal }},
        discount: {{ $this->discountAmount }},

        get total()         { return this.subtotal - this.discount; },
        get cashNum()       { return parseFloat(this.cashAmt)     || 0; },
        get transferNum()   { return parseFloat(this.transferAmt) || 0; },
        get totalPaid()     { return this.payMode === 'cash' ? this.cashNum : this.payMode === 'transfer' ? this.total : this.cashNum + this.transferNum; },
        get change()        { return Math.max(0, this.totalPaid - this.total); },
        get remaining()     { return this.payMode === 'split' ? Math.max(0, this.total - this.cashNum - this.transferNum) : 0; },
        get isValid()       { return this.payMode === 'cash' ? this.cashNum >= this.total : this.payMode === 'transfer' ? true : this.cashNum + this.transferNum >= this.total; },
        get showBreakdown() { return this.payMode === 'split' && (this.cashNum > 0 || this.transferNum > 0); },

        switchMode(m) { this.payMode = m; this.cashAmt = ''; this.transferAmt = ''; },
        onCashInput() {
            if (this.payMode === 'split') {
                const rem = Math.max(0, this.total - this.cashNum);
                this.transferAmt = rem > 0 ? String(rem) : '';
            }
        },
        fmt(n) { return '₦' + Math.round(n).toLocaleString('en-NG'); },
        async complete() {
            if (!this.isValid) return;
            await $wire.completeOrder(
                this.payMode,
                Math.round(this.cashAmt     || 0),
                Math.round(this.transferAmt || 0)
            );
        }
    }"
    style="display:flex;flex-direction:column;height:100%;"
>
    {{-- Header --}}
    <div class="pos-step-header">
        <button wire:click="goTo('summary')" style="border:none;background:none;cursor:pointer;font-size:20px;color:#1a1a18;padding:0;line-height:1;">←</button>
        <span class="pos-step-label">Payment</span>
        <span></span>
    </div>

    {{-- Body --}}
    <div class="pos-scrollable" style="padding:22px 20px;display:flex;flex-direction:column;gap:20px;">

        {{-- Total due --}}
        <div>
            <div class="pos-amt-label">TOTAL DUE</div>
            <div class="pos-serif" style="font-size:34px;font-weight:500;color:#1a1a18;letter-spacing:-0.02em;line-height:1;"
                 x-text="fmt(total)"></div>
            @if($couponCode)
                <div style="font-size:12px;color:#9a9690;margin-top:4px;text-decoration:line-through;">
                    ₦{{ number_format($this->subtotal) }}
                </div>
            @endif
        </div>

        {{-- Coupon --}}
        @if($couponCode)
            <div class="pos-info-card" style="background:#edf7f1;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:12px;font-weight:500;color:#2a7a4b;">{{ $couponCode }}</span>
                    <span style="font-size:12px;color:#2a7a4b;">−₦{{ number_format($this->discountAmount) }} ({{ $couponLabel }})</span>
                </div>
                <button wire:click="removeCoupon" style="border:none;background:none;cursor:pointer;font-size:14px;color:#2a7a4b;padding:0;line-height:1;">×</button>
            </div>
        @else
            <div style="display:flex;align-items:center;gap:8px;">
                <input type="text"
                       wire:model="couponInput"
                       placeholder="Coupon code"
                       class="pos-coupon-input {{ $couponError ? 'error' : '' }}"
                       wire:keydown.enter="applyCoupon" />
                @if($couponError)
                    <span style="font-size:11px;color:#c0392b;flex-shrink:0;">{{ $couponError }}</span>
                @else
                    <button wire:click="applyCoupon" style="border:none;background:none;cursor:pointer;font-size:12px;color:#9a9690;padding:0;flex-shrink:0;">Apply</button>
                @endif
            </div>
        @endif

        {{-- Outstanding balance --}}
        @if(($this->customer?->balance ?? 0) > 0)
            <div class="pos-info-card" style="background:#fdf0e8;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;color:#7a3a18;">Outstanding balance</span>
                <span class="pos-serif" style="font-size:14px;font-weight:500;color:#7a3a18;">₦{{ number_format($this->customer->balance) }}</span>
            </div>
        @endif

        {{-- Pay mode --}}
        <div class="pos-seg-bar">
            <button class="pos-seg-btn" :class="{ active: payMode === 'cash' }"     @click="switchMode('cash')">Cash</button>
            <button class="pos-seg-btn" :class="{ active: payMode === 'transfer' }" @click="switchMode('transfer')">Transfer</button>
            <button class="pos-seg-btn" :class="{ active: payMode === 'split' }"    @click="switchMode('split')">Split</button>
        </div>

        {{-- Cash input --}}
        <div x-show="payMode === 'cash' || payMode === 'split'">
            <div class="pos-amt-label" x-text="payMode === 'split' ? 'CASH' : 'CASH TENDERED'"></div>
            <div class="pos-amt-row">
                <span class="pos-amt-sym">₦</span>
                <input class="pos-amt-input" type="number" placeholder="0" x-model="cashAmt" @input="onCashInput()" />
            </div>
        </div>

        {{-- Transfer display --}}
        <div x-show="payMode === 'transfer'" class="pos-info-card" style="background:#f5f3ef;">
            <div class="pos-amt-label">TRANSFER AMOUNT</div>
            <div class="pos-serif" style="font-size:24px;font-weight:500;color:#1a1a18;" x-text="fmt(total)"></div>
        </div>

        {{-- Transfer input (split) --}}
        <div x-show="payMode === 'split'">
            <div class="pos-amt-label">TRANSFER</div>
            <div class="pos-amt-row">
                <span class="pos-amt-sym">₦</span>
                <input class="pos-amt-input" type="number" placeholder="0" x-model="transferAmt" />
            </div>
        </div>

        {{-- Split breakdown --}}
        <div x-show="showBreakdown" class="pos-info-card" style="background:#f5f3ef;display:flex;flex-direction:column;gap:6px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;color:#9a9690;">
                <span>Cash</span><span x-text="fmt(cashNum)"></span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:12px;color:#9a9690;">
                <span>Transfer</span><span x-text="fmt(transferNum)"></span>
            </div>
            <div style="border-top:0.5px solid #e0dbd3;padding-top:6px;display:flex;justify-content:space-between;font-size:13px;font-weight:500;color:#1a1a18;">
                <span>Total paid</span><span x-text="fmt(cashNum + transferNum)"></span>
            </div>
        </div>

        {{-- Change --}}
        <div x-show="change > 0" class="pos-info-card" style="background:#edf7f1;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:12px;color:#2a7a4b;">Change</span>
            <span class="pos-serif" style="font-size:16px;font-weight:500;color:#2a7a4b;" x-text="fmt(change)"></span>
        </div>

        {{-- Remaining --}}
        <div x-show="payMode === 'split' && remaining > 0 && (cashNum > 0 || transferNum > 0)"
             class="pos-info-card" style="background:#fff5f5;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:12px;color:#c0392b;">Remaining</span>
            <span class="pos-serif" style="font-size:16px;font-weight:500;color:#c0392b;" x-text="fmt(remaining)"></span>
        </div>

    </div>

    {{-- Footer --}}
    <div class="pos-sidebar-footer">
        <button class="pos-gold-btn"
                :style="{ opacity: isValid ? 1 : 0.45, cursor: isValid ? 'pointer' : 'not-allowed' }"
                :disabled="!isValid"
                @click="complete()">
            Complete Order →
        </button>
    </div>
</div>
```

---

### `_step-done.blade.php`

```blade
<div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;">
    <div style="font-size:28px;color:#2a7a4b;">✓</div>
    <div style="font-size:15px;font-weight:500;color:#1a1a18;">Order complete</div>
    <div style="font-size:12px;color:#9a9690;">₦{{ number_format($this->total) }} received</div>
    <button class="pos-gold-btn" style="width:auto;padding:10px 24px;margin-top:12px;" wire:click="resetAll">
        New Order
    </button>
</div>
```

---

## 4. Model Assumptions

Adjust if your actual models differ:

| What the component expects | Likely model/field |
|---|---|
| `$customer->full_name` | Accessor on `Customer` combining first + last name |
| `$customer->phone` | `customers.phone` column |
| `$customer->affiliate->name` | `Customer` belongs to `Affiliate` (or nullable) |
| `$customer->balance` | `customers.balance` column (int, naira) |
| `Product::find($id)->price` | `products.price` column |
| `Coupon->code`, `->discount_percentage`, `->is_active` | Adjust to your actual `coupons` table schema |
| `Order`, `OrderItem`, `Payment` | Adjust column names to match your migrations |

If a `Coupon` model does not exist yet, create a basic one:
```php
// database/migrations/xxxx_create_coupons_table.php
Schema::create('coupons', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->unsignedTinyInteger('discount_percentage');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

---

## 5. Wiring the Left Panel (Product Grid)

The left panel product cards must dispatch a browser event when clicked so the
sidebar's `addProduct` listener fires:

```blade
{{-- In the product card component --}}
<button wire:click="$dispatch('product-clicked', { productId: {{ $product->id }} })">
    ...card content...
</button>
```

Or if the left panel is a separate Livewire component:
```php
$this->dispatch('product-clicked', productId: $product->id);
```

---

## 6. Customer Modal Integration

The POS page has a customer selection modal. When a customer is confirmed, dispatch:
```php
$this->dispatch('customer-selected', customerId: $customer->id);
```
The sidebar listens for this and updates `$customerId` accordingly.

The "edit" link in the order header should open this modal:
```blade
<span x-on:click="$dispatch('open-customer-modal')" ...>edit</span>
```
Wire that event to your existing modal trigger.

---

## 7. Colour & Font Reference

| Token | Value |
|---|---|
| Gold | `#C9A84C` |
| Dark | `#1a1a18` |
| Muted | `#9a9690` |
| Border | `#ece9e3` |
| Border light | `#f0ede8` |
| Monetary font | `'Cormorant Garamond', Georgia, serif` |
| UI font | `'DM Sans', sans-serif` (Filament already loads this) |
| Green (success/coupon) | `#2a7a4b` bg `#edf7f1` |
| Amber (balance due) | `#7a3a18` bg `#fdf0e8` |
| Red (remaining) | `#c0392b` bg `#fff5f5` |

---

## 8. Implementation Order

1. Create the CSS block and main view (`order-sidebar.blade.php`)
2. Create all four partial views
3. Create the Livewire PHP class
4. Register the component (if not using auto-discovery): `Livewire::component('pos.order-sidebar', OrderSidebar::class);`
5. Embed `<livewire:pos.order-sidebar />` in the POS page view
6. Wire up the product grid dispatch event
7. Wire up the customer modal dispatch event
8. Create `coupons` migration + model if needed
9. Test each step in order: add items → delivery date → summary → payment modes → complete
