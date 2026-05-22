<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\CustomerMeasurement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Product;
use App\Models\Service;
use App\Services\AssignmentService;
use App\Services\NotificationService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class Pos extends Page
{
    use WithFileUploads;
    protected string $view = 'filament.pages.pos';
    protected static ?string $navigationLabel = 'POS';
    protected static ?string $title = 'Point of Sale';
    protected static ?int $navigationSort = 0;

    public static function getNavigationIcon(): string { return 'heroicon-o-calculator'; }
    public static function getNavigationGroup(): ?string { return 'Operations'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'cashier']);
    }

    // ── Cart / Order State ─────────────────────────────────
    public string $orderType    = 'tailoring';
    public string $customerName = '';
    public string $customerPhone = '';
    public string $customerEmail = '';
    public string $customerAddress = '';
    public string $deliveryType = 'pickup'; // pickup, delivery
    public string $estimatedCompletionDate = '';
    public string $notes = '';
    public array  $items = [];
    public string $amountPaid    = '';
    public string $paymentMethod = 'cash';

    // ── POS Step: 'order' | 'payment' ─────────────────────
    public string $posStep = 'order';

    // ── Customer search (order step) ───────────────────────
    public string $customerSearch = '';

    // ── Order summary collapsed state ──────────────────────
    public bool $orderSummaryCollapsed = false;

    // ── Product Grid State ─────────────────────────────────
    public string $search = '';

    // ── Production Modal State ─────────────────────────────
    public bool   $showProductModal = false;
    public ?int   $modalProductId   = null;
    public ?int   $modalVariantId   = null;
    public int    $modalStep        = 1; // 1:customer  2:measurements  3:confirm
    public string $modalCustomerSearch = '';
    public ?int   $modalCustomerId     = null;
    public string $modalCustomerName   = '';
    public string $modalCustomerPhone  = '';
    public string $modalCustomerEmail  = '';
    public array  $modalMeasurements   = [];
    public array  $modalBom            = [];
    public bool   $modalWashingRequired = true;
    public string $modalWashingSkipReason = '';
    public string $modalNotes         = '';
    public string $modalDesignNotes   = '';
    public        $modalDesignFile    = null; // Livewire TemporaryUploadedFile

    // ── Customer Modal State ───────────────────────────────
    public bool $showCustomerModal = false;
    public bool $processAfterCustomer = false;

    // ── Post-sale State ────────────────────────────────────
    public bool $showReceipt = false;
    public ?int  $completedOrderId = null;
    public ?int  $customerId = null;

    // ── Lifecycle ──────────────────────────────────────────
    public function mount(): void
    {
        $this->addBlankItem();
    }

    // ── Product Grid ───────────────────────────────────────
    public function getProducts(): Collection
    {
        return Product::where('is_active', true)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('category', 'like', "%{$this->search}%"))
            ->orderBy('sort_order')
            ->get();
    }

    public function getServices(): Collection
    {
        return Service::where('is_active', true)->orderBy('sort_order')->get();
    }

    // ── Product Click Handler ──────────────────────────────
    public function selectProduct(int $productId): void
    {
        $product = Product::find($productId);
        if (! $product) return;

        if ($product->requiresProduction()) {
            $this->openProductionModal($productId);
        } else {
            $this->addProductToCart($product);
        }
    }

    private function addProductToCart(Product $product): void
    {
        $price = (float) $product->price;

        // If already in the cart as a ready-made item, just increment qty
        foreach ($this->items as $idx => $item) {
            if (
                ($item['product_id'] ?? null) === $product->id &&
                ($item['production_type'] ?? 'ready_made') === 'ready_made' &&
                ! empty(trim($item['description'] ?? ''))
            ) {
                $items = $this->items;
                $items[$idx]['qty']     = ($items[$idx]['qty'] ?? 1) + 1;
                $items[$idx]['subtotal'] = round($items[$idx]['qty'] * $price, 2);
                $this->items = $items;
                return;
            }
        }

        // Replace the last blank item if present, otherwise append
        $lastIdx = array_key_last($this->items);
        if ($lastIdx !== null && empty(trim($this->items[$lastIdx]['description'] ?? ''))) {
            $items = $this->items;
            $items[$lastIdx] = $this->makeCartItem($product, $price);
            $this->items = $items;
        } else {
            $this->items[] = $this->makeCartItem($product, $price);
        }
    }

    private function makeCartItem(Product $product, float $price): array
    {
        return [
            'description'          => $product->name,
            'qty'                  => 1,
            'unit_price'           => $price,
            'subtotal'             => $price,
            'product_id'           => $product->id,
            'production_type'      => $product->production_type,
            'production_path_key'  => 'none',
            'customer_id'          => null,
            'measurements'         => [],
            'washing_required'     => true,
            'washing_skipped'      => false,
            'washing_skip_reason'  => '',
        ];
    }

    // ── Production Modal ───────────────────────────────────
    public function openProductionModal(int $productId): void
    {
        $this->modalProductId       = $productId;
        $this->modalVariantId       = null;
        $this->modalStep            = 1;
        $this->modalCustomerSearch  = '';
        $this->modalCustomerId      = null;
        $this->modalCustomerName    = $this->customerName;
        $this->modalCustomerPhone   = $this->customerPhone;
        $this->modalCustomerEmail   = $this->customerEmail;
        $this->modalMeasurements    = [];
        $this->modalBom             = [];
        $this->modalWashingRequired = true;
        $this->modalWashingSkipReason = '';
        $this->modalNotes           = '';
        $this->modalDesignNotes     = '';
        $this->modalDesignFile      = null;

        // Pre-load BOM from product materials
        $product = Product::with(['materials.material', 'measurementTemplate', 'variants' => fn ($q) => $q->where('is_active', true)->orderBy('variant_type')])->find($productId);
        if ($product) {
            foreach ($product->materials as $m) {
                $this->modalBom[] = [
                    'id'       => $m->id,
                    'name'     => $m->material?->name ?? '—',
                    'quantity' => (float) $m->quantity,
                    'unit'     => $m->material?->unit ?? '',
                ];
            }
        }

        // If a customer is already known, pre-fill their saved measurements for this product
        if ($this->customerId) {
            $this->modalCustomerId = $this->customerId;
            $this->prefillModalMeasurements();
        }

        $this->showProductModal = true;
    }

    public function closeProductionModal(): void
    {
        $this->showProductModal = false;
        $this->modalProductId   = null;
    }

    public function getModalProduct(): ?Product
    {
        if (! $this->modalProductId) return null;
        return Product::with(['materials', 'measurementTemplate', 'variants' => fn ($q) => $q->where('is_active', true)->orderBy('variant_type')])->find($this->modalProductId);
    }

    // ── Customer Modal ─────────────────────────────────────
    public function openCustomerModal(bool $processAfter = false): void
    {
        $this->processAfterCustomer = $processAfter;
        $this->showCustomerModal    = true;
    }

    public function closeCustomerModal(): void
    {
        $this->showCustomerModal    = false;
        $this->processAfterCustomer = false;
    }

    public function saveCustomerFromModal(): void
    {
        $this->validate([
            'customerName'  => ['required', 'string', 'min:2'],
            'customerPhone' => ['required', 'string', 'min:7'],
        ]);

        $this->showCustomerModal = false;

        if ($this->processAfterCustomer) {
            $this->processAfterCustomer = false;
            $this->processOrder();
        }
    }

    // ── Order-step customer search ─────────────────────────
    public function getSearchCustomers(): \Illuminate\Support\Collection
    {
        if (strlen($this->customerSearch) < 2) return collect();
        return Customer::where('name', 'like', "%{$this->customerSearch}%")
            ->orWhere('phone', 'like', "%{$this->customerSearch}%")
            ->limit(6)
            ->get();
    }

    public function selectCustomer(int $customerId): void
    {
        $customer = Customer::find($customerId);
        if (! $customer) return;

        $this->customerName   = $customer->name;
        $this->customerPhone  = $customer->phone;
        $this->customerEmail  = $customer->email ?? '';
        $this->customerSearch = '';
    }

    public function getModalCustomers(): \Illuminate\Support\Collection
    {
        if (strlen($this->modalCustomerSearch) < 2) return collect();
        return Customer::where('name', 'like', "%{$this->modalCustomerSearch}%")
            ->orWhere('phone', 'like', "%{$this->modalCustomerSearch}%")
            ->limit(6)
            ->get();
    }

    public function selectModalCustomer(int $customerId): void
    {
        $customer = Customer::find($customerId);
        if (! $customer) return;

        $this->modalCustomerId    = $customer->id;
        $this->modalCustomerName  = $customer->name;
        $this->modalCustomerPhone = $customer->phone;
        $this->modalCustomerEmail = $customer->email ?? '';
        $this->modalCustomerSearch = '';

        $this->prefillModalMeasurements();
    }

    private function prefillModalMeasurements(): void
    {
        if (! $this->modalCustomerId || ! $this->modalProductId) return;

        $saved = \App\Models\CustomerMeasurement::where('customer_id', $this->modalCustomerId)
            ->where('product_id', $this->modalProductId)
            ->first();

        if ($saved && ! empty($saved->measurements)) {
            $this->modalMeasurements = $saved->measurements;
        }
    }

    // Steps: 1 = customer/variant, 2 = design, 3 = measurements (if any), 4 = confirm
    public function modalNext(): void
    {
        if ($this->modalStep === 1) {
            if (empty(trim($this->modalCustomerName)) || empty(trim($this->modalCustomerPhone))) {
                Notification::make()->title('Customer name and phone are required.')->danger()->send();
                return;
            }
        }
        $product   = $this->getModalProduct();
        $hasFields = $product && $product->measurementTemplate && ! empty($product->measurementTemplate->fields);

        if ($this->modalStep === 2 && ! $hasFields) {
            $this->modalStep = 4; // skip measurements
            return;
        }
        $this->modalStep = min(4, $this->modalStep + 1);
    }

    public function modalPrev(): void
    {
        $product   = $this->getModalProduct();
        $hasFields = $product && $product->measurementTemplate && ! empty($product->measurementTemplate->fields);

        if ($this->modalStep === 4 && ! $hasFields) {
            $this->modalStep = 2; // skip back over measurements
            return;
        }
        $this->modalStep = max(1, $this->modalStep - 1);
    }

    public function confirmProductionItem(): void
    {
        $product = $this->getModalProduct();
        if (! $product) return;

        $price = (float) $product->price;

        // Replace trailing blank item if present
        $lastIdx = array_key_last($this->items);
        $hasTrailingBlank = $lastIdx !== null && empty(trim($this->items[$lastIdx]['description'] ?? ''));

        // Persist uploaded design file now so the path (string) can sit in the cart
        $designFilePath = null;
        if ($this->modalDesignFile) {
            $raw = $this->modalDesignFile->store('orders/designs', 'public');
            $designFilePath = \App\Support\DesignFileProcessor::process($raw);
            $this->modalDesignFile = null;
        }

        $newItem = [
            'description'          => $product->name,
            'qty'                  => 1,
            'unit_price'           => $price,
            'subtotal'             => $price,
            'product_id'           => $product->id,
            'variant_id'           => $this->modalVariantId,
            'production_type'      => 'production',
            'production_path_key'  => 'sewing_only',
            'customer_id'          => $this->modalCustomerId,
            '_customer_name'       => $this->modalCustomerName,
            '_customer_phone'      => $this->modalCustomerPhone,
            '_customer_email'      => $this->modalCustomerEmail,
            'measurements'         => $this->modalMeasurements,
            'bom'                  => $this->modalBom,
            'washing_required'     => $this->modalWashingRequired,
            'washing_skipped'      => ! $this->modalWashingRequired,
            'washing_skip_reason'  => $this->modalWashingSkipReason,
            '_item_notes'          => $this->modalNotes,
            'design_notes'         => trim($this->modalDesignNotes) ?: null,
            'design_file'          => $designFilePath,
        ];

        if ($hasTrailingBlank) {
            $items = $this->items;
            $items[$lastIdx] = $newItem;
            $this->items = $items;
        } else {
            $this->items[] = $newItem;
        }

        // Prefill global customer if not yet set
        if (empty($this->customerName)) {
            $this->customerName  = $this->modalCustomerName;
            $this->customerPhone = $this->modalCustomerPhone;
            $this->customerEmail = $this->modalCustomerEmail;
        }

        $this->showProductModal = false;
        $this->modalProductId   = null;
    }

    // ── Manual Item Management ─────────────────────────────
    public function addBlankItem(): void
    {
        $this->items[] = [
            'description'         => '',
            'qty'                 => 1,
            'unit_price'          => '',
            'subtotal'            => 0,
            'product_id'          => null,
            'production_type'     => 'ready_made',
            'production_path_key' => 'none',
            'customer_id'         => null,
            'measurements'        => [],
            'washing_required'    => true,
            'washing_skipped'     => false,
            'washing_skip_reason' => '',
        ];
    }

    public function addItem(): void
    {
        $this->addBlankItem();
    }

    public function addServiceItem(int $serviceId): void
    {
        $service = Service::find($serviceId);
        if (! $service) return;

        $price = (float) ($service->base_price ?? 0);
        $this->items[] = [
            'description'         => $service->name,
            'qty'                 => 1,
            'unit_price'          => $price,
            'subtotal'            => $price,
            'product_id'          => null,
            'production_type'     => 'ready_made',
            'production_path_key' => 'none',
            'customer_id'         => null,
            'measurements'        => [],
            'washing_required'    => false,
            'washing_skipped'     => false,
            'washing_skip_reason' => '',
        ];
    }

    public function removeItem(int $index): void
    {
        $items = $this->items;
        array_splice($items, $index, 1);
        $this->items = array_values($items);

        if (empty($this->items)) {
            $this->addBlankItem();
        }
    }

    public function updatedItems(mixed $value, string $key): void
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = (int) $parts[0];
            $qty   = (float) ($this->items[$index]['qty']        ?? 1);
            $price = (float) ($this->items[$index]['unit_price'] ?? 0);
            $items = $this->items;
            $items[$index]['subtotal'] = round($qty * $price, 2);
            $this->items = $items;
        }
    }

    // ── Step Navigation ────────────────────────────────────
    public function processOrder(): void
    {
        if (empty(trim($this->customerName)) || empty(trim($this->customerPhone))) {
            $this->openCustomerModal(true);
            return;
        }

        $this->validate([
            'customerName'  => ['required', 'string', 'min:2'],
            'customerPhone' => ['required', 'string', 'min:7'],
            'items'         => ['required', 'array', 'min:1'],
        ]);

        // Ensure at least one item has a description
        $hasItem = collect($this->items)->contains(fn ($i) => ! empty(trim($i['description'] ?? '')));
        if (! $hasItem) {
            $this->addError('items', 'Please add at least one item to the order.');
            return;
        }

        // Save/update customer immediately so they exist before payment is taken
        $fillable = ['name' => $this->customerName];
        if ($this->customerEmail)   $fillable['email']   = $this->customerEmail;
        if ($this->customerAddress) $fillable['address']  = $this->customerAddress;

        $customer = Customer::updateOrCreate(
            ['phone' => $this->customerPhone],
            $fillable
        );
        $this->customerId = $customer->id;

        $this->posStep = 'payment';
    }

    public function backToOrder(): void
    {
        $this->posStep = 'order';
    }

    // ── Computed ───────────────────────────────────────────
    public function getTotal(): float
    {
        return round(collect($this->items)->sum(fn ($i) => (float) ($i['subtotal'] ?? 0)), 2);
    }

    public function getChange(): float
    {
        return max(0, round((float) $this->amountPaid - $this->getTotal(), 2));
    }

    public function getBalance(): float
    {
        return max(0, round($this->getTotal() - (float) $this->amountPaid, 2));
    }

    public function getCompletedOrder(): ?Order
    {
        if (! $this->completedOrderId) return null;
        return Order::with('items')->find($this->completedOrderId);
    }

    // ── Validation ─────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'customerName'        => ['required', 'string', 'min:2'],
            'customerPhone'       => ['required', 'string', 'min:7'],
            'orderType'           => ['required', 'string'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string'],
            'items.*.qty'         => ['required', 'numeric', 'min:1'],
            'items.*.unit_price'  => ['required', 'numeric', 'min:0'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'customerName'        => 'customer name',
            'customerPhone'       => 'phone number',
            'items.*.description' => 'item description',
            'items.*.qty'         => 'quantity',
            'items.*.unit_price'  => 'unit price',
        ];
    }

    // ── Complete Sale ──────────────────────────────────────
    public function completeSale(): void
    {
        $this->validate();

        $total      = $this->getTotal();
        $amountPaid = (float) $this->amountPaid;

        $minDeposit = $total * 0.5;
        if ($amountPaid < $minDeposit) {
            Notification::make()
                ->title('Minimum deposit required')
                ->body('At least 50% (₦' . number_format($minDeposit, 0) . ') must be paid before confirming this order.')
                ->danger()
                ->send();
            return;
        }

        $paymentStatus = match (true) {
            $amountPaid <= 0     => 'unpaid',
            $amountPaid < $total => 'partial',
            default              => 'paid',
        };

        // Customer was already saved in processOrder(); fall back to upsert if somehow missing
        $customer = $this->customerId
            ? Customer::find($this->customerId)
            : null;

        if (! $customer) {
            $fillable = ['name' => $this->customerName];
            if ($this->customerEmail)   $fillable['email']   = $this->customerEmail;
            if ($this->customerAddress) $fillable['address']  = $this->customerAddress;
            $customer = Customer::updateOrCreate(['phone' => $this->customerPhone], $fillable);
        }

        $order = Order::create([
            'customer_id'      => $customer->id,
            'customer_name'    => $this->customerName,
            'customer_email'   => $this->customerEmail ?: null,
            'customer_phone'   => $this->customerPhone,
            'customer_address' => $this->customerAddress ?: null,
            'type'             => $this->orderType,
            'status'           => 'confirmed',
            'notes'            => $this->notes ?: null,
            'total_amount'     => $total,
            'amount_paid'      => $amountPaid,
            'payment_status'   => $paymentStatus,
            'delivery_type'             => $this->deliveryType,
            'estimated_completion_date' => $this->estimatedCompletionDate ?: null,
        ]);

        if ($amountPaid > 0) {
            $order->recordPayment($amountPaid, $this->paymentMethod, 'Initial payment via POS.');
        }

        $hasProduction = false;

        foreach ($this->items as $item) {
            if (empty(trim($item['description'] ?? ''))) continue;

            // Resolve item-level customer (production items can have their own)
            $itemCustomerId = $item['customer_id'] ?? $customer->id;
            if (! $itemCustomerId) $itemCustomerId = $customer->id;

            $pathKey        = $item['production_path_key'] ?? 'none';
            $productionPath = OrderItem::PATHS[$pathKey] ?? [];
            $isProduction   = ! empty($productionPath);

            $orderItem = OrderItem::create([
                'order_id'            => $order->id,
                'customer_id'         => $itemCustomerId,
                'product_id'          => $item['product_id'] ?? null,
                'variant_id'          => $item['variant_id'] ?? null,
                'description'         => $item['description'],
                'quantity'            => (int)   ($item['qty']        ?? 1),
                'unit_price'          => (float) ($item['unit_price'] ?? 0),
                'subtotal'            => (float) ($item['subtotal']   ?? 0),
                'production_type'     => $isProduction ? 'production' : 'ready_made',
                'production_path'     => $isProduction ? $productionPath : null,
                'item_stage'          => $isProduction ? $productionPath[0] : 'pending',
                'measurements'        => $item['measurements'] ?? null,
                'design_notes'        => $item['design_notes'] ?? null,
                'design_file'         => $item['design_file'] ?? null,
                'washing_required'    => $item['washing_required'] ?? true,
                'washing_skipped'     => $item['washing_skipped'] ?? false,
                'washing_skip_reason' => $item['washing_skip_reason'] ?? null,
                'stage_updated_at'    => now(),
            ]);

            if ($isProduction) {
                $hasProduction = true;

                // Persist measurements to the customer's profile for this product
                if (! empty($orderItem->measurements) && $orderItem->product_id) {
                    \App\Models\CustomerMeasurement::updateOrCreate(
                        ['customer_id' => $itemCustomerId, 'product_id' => $orderItem->product_id],
                        ['measurements' => $orderItem->measurements, 'unit' => 'inches']
                    );
                }
            }
        }

        OrderStatusLog::create([
            'order_id'      => $order->id,
            'changed_by'    => auth()->id(),
            'status'        => 'confirmed',
            'notes'         => 'Created via POS — ' . strtoupper($this->paymentMethod),
            'is_published'  => true,
            'client_message'=> 'Your order has been received and confirmed.',
        ]);

        // Notify customer
        try {
            app(NotificationService::class)->orderConfirmed($order);
        } catch (\Throwable) {
            // Notification failure must not block sale completion
        }

        $this->completedOrderId = $order->id;
        $this->showReceipt = true;

        Notification::make()
            ->title('Sale completed — ' . $order->reference)
            ->success()
            ->send();
    }

    public function newSale(): void
    {
        $this->reset([
            'customerName', 'customerPhone', 'customerEmail', 'customerAddress',
            'notes', 'items', 'amountPaid', 'completedOrderId', 'showReceipt',
            'customerId', 'estimatedCompletionDate', 'search', 'showProductModal', 'modalProductId',
            'modalDesignNotes', 'modalDesignFile', 'showCustomerModal', 'processAfterCustomer',
        ]);
        $this->orderType     = 'tailoring';
        $this->paymentMethod = 'cash';
        $this->deliveryType  = 'pickup';
        $this->posStep       = 'order';
        $this->addBlankItem();
    }
}
