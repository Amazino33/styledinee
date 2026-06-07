<?php

namespace App\Livewire\Pos;

use App\Models\Customer;
use App\Services\CouponService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class OrderSidebar extends Component
{
    // ── Props from parent Pos page (re-synced on each parent render) ─────────
    #[Reactive] public array  $items                   = [];
    #[Reactive] public ?int   $customerId              = null;
    #[Reactive] public string $customerName            = '';
    #[Reactive] public string $customerPhone           = '';
    #[Reactive] public string $customerAddress         = '';
    #[Reactive] public string $deliveryType            = 'pickup';
    #[Reactive] public string $estimatedCompletionDate = '';
    #[Reactive] public string $notes                   = '';

    // ── Sidebar-owned state ───────────────────────────────────────────────────
    /** order | summary | payment | done */
    public string $step          = 'order';
    public string $couponInput   = '';
    public string $couponCode    = '';
    public float  $couponDiscount = 0.0;
    public string $couponLabel   = '';
    public string $couponError   = '';

    // ── Computed ──────────────────────────────────────────────────────────────
    #[Computed]
    public function cartItems(): array
    {
        // Preserve original indices so qty/remove buttons reference the correct $items slot
        return collect($this->items)
            ->filter(fn ($i) => !empty(trim($i['description'] ?? '')))
            ->all();
    }

    #[Computed]
    public function subtotal(): float
    {
        return round(collect($this->cartItems())->sum(fn ($i) => (float) ($i['subtotal'] ?? 0)), 2);
    }

    #[Computed]
    public function discountAmount(): float
    {
        return round($this->couponDiscount, 2);
    }

    #[Computed]
    public function total(): float
    {
        return max(0.0, round($this->subtotal() - $this->discountAmount(), 2));
    }

    #[Computed]
    public function customer(): ?Customer
    {
        return $this->customerId ? Customer::find($this->customerId) : null;
    }

    // ── Step navigation ───────────────────────────────────────────────────────
    public function goTo(string $step): void
    {
        $this->step = $step;
    }

    // ── Coupon ────────────────────────────────────────────────────────────────
    public function applyCoupon(): void
    {
        $this->couponError = '';
        $code = trim($this->couponInput);

        if (empty($code)) {
            $this->couponError = 'Enter a coupon code.';
            return;
        }

        $cartItems = collect($this->cartItems())
            ->map(fn ($i) => [
                'product_id' => $i['product_id'] ?? null,
                'subtotal'   => (float) ($i['subtotal'] ?? 0),
            ])
            ->all();

        $result = app(CouponService::class)->validate(
            $code,
            $this->subtotal(),
            $this->customer(),
            $cartItems
        );

        if (! $result['valid']) {
            $this->couponError = $result['message'];
            return;
        }

        $this->couponCode     = $result['coupon']->code;
        $this->couponDiscount = (float) $result['discount'];
        $this->couponLabel    = $result['message'];
        $this->couponInput    = '';
    }

    public function removeCoupon(): void
    {
        $this->couponCode     = '';
        $this->couponDiscount = 0.0;
        $this->couponLabel    = '';
        $this->couponInput    = '';
        $this->couponError    = '';
    }

    // ── Item mutations (dispatched to parent Pos page via Livewire events) ────
    public function removeItem(int $index): void
    {
        $this->dispatch('pos-remove-item', index: $index);
    }

    public function incrementQty(int $index): void
    {
        $this->dispatch('pos-increment-qty', index: $index);
    }

    public function decrementQty(int $index): void
    {
        if (($this->items[$index]['qty'] ?? 1) <= 1) {
            $this->dispatch('pos-remove-item', index: $index);
        } else {
            $this->dispatch('pos-decrement-qty', index: $index);
        }
    }

    public function updateItem(int $index, string $field, mixed $value): void
    {
        $this->dispatch('pos-update-item', index: $index, field: $field, value: $value);
    }

    public function addLine(): void
    {
        $this->dispatch('pos-add-line');
    }

    public function clearCart(): void
    {
        $this->dispatch('pos-clear-cart');
    }

    public function setDeliveryType(string $type): void
    {
        $this->dispatch('pos-set-delivery-type', type: $type);
    }

    public function setEstimatedDate(string $date): void
    {
        $this->dispatch('pos-set-estimated-date', date: $date);
    }

    public function setNotes(string $notes): void
    {
        $this->dispatch('pos-set-notes', notes: $notes);
    }

    public function openCustomerModal(): void
    {
        $this->dispatch('pos-open-customer-modal');
    }

    // ── Proceed from summary → payment (parent validates customer first) ───────
    public function proceedToPayment(): void
    {
        $this->dispatch('pos-proceed-payment');
    }

    // ── Complete order — called from Alpine, finalised in parent ──────────────
    public function completeOrder(string $payMode, float $cashAmt, float $transferAmt): void
    {
        $this->dispatch('pos-complete-order',
            payMode:        $payMode,
            cashAmt:        $cashAmt,
            transferAmt:    $transferAmt,
            couponCode:     $this->couponCode,
            couponDiscount: $this->couponDiscount,
        );
    }

    // ── Listeners from parent ─────────────────────────────────────────────────
    #[On('pos-step-payment')]
    public function handleStepPayment(): void
    {
        $this->step = 'payment';
    }

    #[On('order-sale-done')]
    public function handleOrderSaleDone(): void
    {
        $this->step = 'done';
    }

    #[On('new-sale-started')]
    public function handleNewSaleStarted(): void
    {
        $this->step = 'order';
        $this->removeCoupon();
    }

    // ── New sale (from done step) ─────────────────────────────────────────────
    public function newSale(): void
    {
        $this->dispatch('pos-new-sale');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pos.order-sidebar');
    }
}
