<?php

namespace App\Filament\Pages;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerMeasurement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Product;
use App\Models\Service;
use App\Services\AssignmentService;
use App\Services\CouponService;
use App\Services\NotificationService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class Pos extends Page
{
    use WithFileUploads;
    protected string $view = 'filament.pages.pos';
    protected static ?string $navigationLabel = 'POS';
    protected static ?string $title = 'Point of Sale';
    protected static ?int $navigationSort = 0;

    public static function getNavigationIcon(): string { return 'heroicon-o-calculator'; }
    public static function getNavigationGroup(): ?string { return 'Point of Sale'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:Pos') ?? false;
    }

    // â”€â”€ Cart / Order State â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public string $orderType    = 'tailoring';
    public string $customerName = '';
    public string $customerPhone = '';
    public string $customerEmail = '';
    public string $customerAddress = '';
    public string $deliveryType = 'pickup'; // pickup, delivery
    public string $estimatedCompletionDate = '';
    public string $notes = '';
    public array  $items = [];
    // Split payments: each entry has 'method' and 'amount'
    public array $splits = [
        ['method' => 'cash', 'amount' => ''],
    ];

    // â”€â”€ POS Step: 'order' | 'payment' (kept for session compat) â”€â”€â”€â”€â”€
    public string $posStep = 'order';

    // â”€â”€ Coupon data passed in from the sidebar before completeSale() â”€
    public float  $sidebarCouponDiscount = 0.0;
    public string $sidebarCouponCode     = '';

    // â”€â”€ Customer search (order step) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public string $customerSearch = '';

    // â”€â”€ Order summary collapsed state â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public bool $orderSummaryCollapsed = false;

    // â”€â”€ Product Grid State â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public string $search              = '';
    public ?int   $categoryFilter      = null;
    public bool   $showCategoryModal   = false;
    public string $categoryModalSearch = '';

    // â”€â”€ Variant Picker Modal (ready-made products with variants) â”€â”€
    public bool  $showVariantModal          = false;
    public ?int  $variantModalProductId     = null;
    public ?int  $variantModalSelectedId    = null;

    public function openVariantModal(int $productId): void
    {
        $this->variantModalProductId  = $productId;
        $this->variantModalSelectedId = null;
        $this->showVariantModal       = true;
    }

    public function closeVariantModal(): void
    {
        $this->showVariantModal      = false;
        $this->variantModalProductId = null;
        $this->variantModalSelectedId = null;
    }

    public function confirmVariantSelection(): void
    {
        $product = Product::with(['variants' => fn ($q) => $q->where('is_active', true)])
            ->find($this->variantModalProductId);
        if (! $product) { $this->closeVariantModal(); return; }

        $variant = $this->variantModalSelectedId
            ? $product->variants->firstWhere('id', $this->variantModalSelectedId)
            : null;

        $this->addProductToCart($product, $variant);
        $this->closeVariantModal();
    }

    // â”€â”€ Production Modal State â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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
    public array  $modalBomRemovals    = [];
    public bool   $showModalBomRemove  = false;
    public int    $modalBomRemoveIndex = -1;
    public string $modalBomRemoveReason = '';
    public bool   $modalWashingRequired = true;
    public string $modalWashingSkipReason = '';
    public string $modalDeliveryType    = 'pickup';
    public string $modalDeliveryAddress = '';
    public string $modalDeliveryDate    = '';
    public string $modalNotes         = '';
    public string $modalDesignNotes   = '';
    public        $modalDesignFile    = null; // Livewire TemporaryUploadedFile

    // â”€â”€ Customer Modal State â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public bool $showCustomerModal = false;
    public bool $processAfterCustomer = false;

    // â”€â”€ BOM Removal Modal State â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public bool   $showRemoveBomModal = false;
    public int    $removeBomItemIndex = -1;
    public int    $removeBomBomIndex  = -1;
    public string $removeBomReason    = '';

    // â”€â”€ Add BOM Inline State â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public int    $addBomItemIndex = -1;
    public string $addBomSearch    = '';
    public array  $addBomResults   = [];
    public ?int   $addBomProductId = null;
    public string $addBomQty       = '';
    public string $addBomUnit      = '';
    public string $addBomUnitPrice = '';

    // â”€â”€ Modal Add BOM State â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public bool   $modalAddBomOpen      = false;
    public string $modalAddBomSearch    = '';
    public array  $modalAddBomResults   = [];
    public ?int   $modalAddBomProductId = null;
    public string $modalAddBomQty       = '';
    public string $modalAddBomUnit      = '';
    public string $modalAddBomUnitPrice = '';

    // â”€â”€ Post-sale State â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public bool $showReceipt = false;
    public ?int  $completedOrderId = null;
    public ?int  $customerId = null;

    // â”€â”€ Lifecycle â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function mount(): void
    {
        $saved = session()->get($this->cartSessionKey());

        if ($saved) {
            $this->orderType               = $saved['orderType']               ?? 'tailoring';
            $this->customerName            = $saved['customerName']            ?? '';
            $this->customerPhone           = $saved['customerPhone']           ?? '';
            $this->customerEmail           = $saved['customerEmail']           ?? '';
            $this->customerAddress         = $saved['customerAddress']         ?? '';
            $this->deliveryType            = $saved['deliveryType']            ?? 'pickup';
            $this->estimatedCompletionDate = $saved['estimatedCompletionDate'] ?? '';
            $this->notes                   = $saved['notes']                   ?? '';
            $this->items                   = $saved['items']                   ?? [];
            $this->splits                  = $saved['splits']                  ?? [['method' => 'cash', 'amount' => '']];
            $this->posStep                 = $saved['posStep']                 ?? 'order';
            $this->customerId              = $saved['customerId']              ?? null;

            if (empty($this->items)) {
                $this->addBlankItem();
            }
        } else {
            $this->addBlankItem();
        }
    }

    public function dehydrate(): void
    {
        if (! $this->showReceipt) {
            $this->saveCartToSession();
        }
    }

    private function cartSessionKey(): string
    {
        return 'pos_cart_' . auth()->id();
    }

    private function saveCartToSession(): void
    {
        session()->put($this->cartSessionKey(), [
            'orderType'               => $this->orderType,
            'customerName'            => $this->customerName,
            'customerPhone'           => $this->customerPhone,
            'customerEmail'           => $this->customerEmail,
            'customerAddress'         => $this->customerAddress,
            'deliveryType'            => $this->deliveryType,
            'estimatedCompletionDate' => $this->estimatedCompletionDate,
            'notes'                   => $this->notes,
            'items'                   => $this->items,
            'splits'                  => $this->splits,
            'posStep'                 => $this->posStep,
            'customerId'              => $this->customerId,
        ]);
    }

    private function clearCartSession(): void
    {
        session()->forget($this->cartSessionKey());
    }

    // â”€â”€ Product Grid â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function getProducts(): Collection
    {
        $search         = $this->search;
        $categoryFilter = $this->categoryFilter;

        return Product::with('orderType')
            ->where('is_active', true)
            ->when($categoryFilter && ! $search, function ($q) use ($categoryFilter) {
                $cat = \App\Models\OrderType::with('children.children.children.children')
                    ->find($categoryFilter);
                if ($cat) {
                    $q->whereIn('order_type_id', $cat->selfAndDescendantIds());
                }
            })
            ->when($search, fn ($q) => $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('category', 'like', '%' . $search . '%'))
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * All active categories in depth-first tree order (root first, children nested).
     * Used for the main grid (take 8) and the "See All" modal.
     */
    public function getCategories(): BaseCollection
    {
        $all    = \App\Models\OrderType::where('is_active', true)->orderBy('sort_order')->get()->keyBy('id');
        $result = collect();
        $visited = [];

        $walk = function ($parentId) use (&$walk, $all, &$result, &$visited): void {
            foreach ($all->where('parent_id', $parentId)->sortBy('sort_order') as $cat) {
                if (isset($visited[$cat->id])) continue;
                $visited[$cat->id] = true;
                $result->push($cat);
                $walk($cat->id);
            }
        };

        $walk(null);
        return $result;
    }

    /**
     * Categories filtered by the modal search term, preserving tree order.
     */
    public function getModalCategories(): BaseCollection
    {
        $term = strtolower(trim($this->categoryModalSearch));
        $all  = $this->getCategories();

        if ($term === '') return $all;

        return $all->filter(fn ($cat) => str_contains(strtolower($cat->name), $term))->values();
    }

    public function selectCategory(int $id, string $slug): void
    {
        $this->orderType          = $slug;
        $this->categoryFilter     = $id > 0 ? $id : null;
        $this->showCategoryModal  = false;
    }

    public function openCategoryModal(): void
    {
        $this->categoryModalSearch = '';
        $this->showCategoryModal   = true;
    }

    public function closeCategoryModal(): void
    {
        $this->showCategoryModal = false;
    }

    private function categoryPathKey(): string
    {
        if (! $this->categoryFilter) return 'none';
        return \App\Models\OrderType::with('parent.parent.parent')
            ->find($this->categoryFilter)?->effective_default_path_key ?? 'none';
    }

    public function getServices(): Collection
    {
        return Service::where('is_active', true)->orderBy('sort_order')->get();
    }

    // â”€â”€ Product Click Handler â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function selectProduct(int $productId): void
    {
        $product = Product::with(['variants' => fn ($q) => $q->where('is_active', true)])
            ->find($productId);
        if (! $product) return;

        if ($product->requiresProduction()) {
            $this->openProductionModal($productId);
        } elseif ($product->variants->isNotEmpty()) {
            $this->openVariantModal($productId);
        } else {
            $this->addProductToCart($product);
        }
    }

    private function addProductToCart(Product $product, ?\App\Models\ProductVariant $variant = null): void
    {
        $price = (float) $product->price + (float) ($variant?->price_adjustment ?? 0);

        // If same product+variant already in cart, just increment qty
        foreach ($this->items as $idx => $item) {
            if (
                ($item['product_id'] ?? null) === $product->id &&
                ($item['variant_id'] ?? null) === ($variant?->id) &&
                ($item['production_type'] ?? 'ready_made') === 'ready_made'
            ) {
                $items = $this->items;
                $items[$idx]['qty']      = ($items[$idx]['qty'] ?? 1) + 1;
                $items[$idx]['subtotal'] = round($items[$idx]['qty'] * $price, 2);
                $this->items = $items;
                return;
            }
        }

        // Replace last blank item if present, otherwise append
        $lastIdx = array_key_last($this->items);
        if ($lastIdx !== null && empty(trim($this->items[$lastIdx]['description'] ?? ''))) {
            $items = $this->items;
            $items[$lastIdx] = $this->makeCartItem($product, $price, $variant);
            $this->items = $items;
        } else {
            $this->items[] = $this->makeCartItem($product, $price, $variant);
        }

        $this->autoFillEstimatedDate();
    }

    private function makeCartItem(Product $product, float $price, ?\App\Models\ProductVariant $variant = null): array
    {
        $description = $product->name;
        if ($variant) {
            $description .= ' â€” ' . ucfirst($variant->variant_type) . ': ' . $variant->variant_value;
        }

        $product->loadMissing('materials.material');
        $bom = $product->materials->map(function ($m) {
            $unitPrice = (float) ($m->material?->price ?? 0);
            return [
                'id'         => $m->id,
                'name'       => $m->material?->name ?? '—',
                'quantity'   => (float) $m->quantity,
                'unit'       => $m->material?->unit ?? '',
                'unit_price' => $unitPrice,
                'line_total' => round($unitPrice * (float) $m->quantity, 2),
            ];
        })->values()->all();

        return [
            'description'          => $description,
            'qty'                  => 1,
            'unit_price'           => $price,
            'subtotal'             => $price,
            'product_id'           => $product->id,
            'variant_id'           => $variant?->id,
            'production_type'      => $product->production_type,
            'production_path_key'  => \App\Models\OrderItem::detectPath($product, $this->categoryPathKey()),
            'customer_id'          => null,
            'measurements'         => [],
            'bom'                  => $bom,
            'bom_removals'         => [],
            'delivery_type'        => 'pickup',
            'washing_required'     => true,
            'washing_skipped'      => false,
            'washing_skip_reason'  => '',
        ];
    }

    public function openRemoveBomModal(int $itemIndex, int $bomIndex): void
    {
        $this->removeBomItemIndex = $itemIndex;
        $this->removeBomBomIndex  = $bomIndex;
        $this->removeBomReason    = '';
        $this->showRemoveBomModal = true;
    }

    public function confirmRemoveBomLine(): void
    {
        $this->validate(
            ['removeBomReason' => 'required|min:3'],
            ['removeBomReason.required' => 'A reason is required.', 'removeBomReason.min' => 'Reason must be at least 3 characters.']
        );

        $items = $this->items;
        $i  = $this->removeBomItemIndex;
        $bi = $this->removeBomBomIndex;

        if (isset($items[$i]['bom'][$bi])) {
            $line = $items[$i]['bom'][$bi];
            $items[$i]['bom_removals'][] = [
                'name'       => $line['name'],
                'quantity'   => $line['quantity'],
                'unit'       => $line['unit'],
                'reason'     => trim($this->removeBomReason),
                'removed_by' => auth()->user()?->name ?? 'Staff',
                'removed_at' => now()->format('g:i A'),
            ];
            array_splice($items[$i]['bom'], $bi, 1);
            $items[$i]['bom'] = array_values($items[$i]['bom']);
            $this->items = $items;
        }

        $this->showRemoveBomModal = false;
        $this->removeBomReason    = '';
        $this->removeBomItemIndex = -1;
        $this->removeBomBomIndex  = -1;
    }

    public function cancelRemoveBom(): void
    {
        $this->showRemoveBomModal = false;
        $this->removeBomReason    = '';
    }

    // â"€â"€ Add BOM Inline â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€
    public function toggleAddBom(int $itemIndex): void
    {
        if ($this->addBomItemIndex === $itemIndex) {
            $this->cancelAddBom();
        } else {
            $this->cancelAddBom();
            $this->addBomItemIndex = $itemIndex;
        }
    }

    public function updatedAddBomSearch(): void
    {
        $this->addBomProductId = null;
        $this->addBomUnit      = '';
        $this->addBomUnitPrice = '';

        if (strlen(trim($this->addBomSearch)) < 2) {
            $this->addBomResults = [];
            return;
        }

        $this->addBomResults = Product::where('is_material', true)
            ->where('is_active', true)
            ->where('name', 'like', '%' . trim($this->addBomSearch) . '%')
            ->limit(6)
            ->get(['id', 'name', 'unit', 'price'])
            ->map(fn ($p) => [
                'id'         => $p->id,
                'name'       => $p->name,
                'unit'       => $p->unit ?? '',
                'unit_price' => (float) $p->price,
            ])
            ->all();
    }

    public function selectBomResult(int $productId): void
    {
        $product = Product::find($productId);
        if (! $product) return;

        $this->addBomProductId = $productId;
        $this->addBomSearch    = $product->name;
        $this->addBomUnit      = $product->unit ?? '';
        $this->addBomUnitPrice = (string) $product->price;
        $this->addBomResults   = [];
    }

    public function confirmAddBomLine(): void
    {
        $this->validate([
            'addBomSearch'    => ['required', 'string', 'min:2'],
            'addBomQty'       => ['required', 'numeric', 'min:0.001'],
            'addBomUnitPrice' => ['required', 'numeric', 'min:0'],
        ], [
            'addBomSearch.required'    => 'Material name is required.',
            'addBomSearch.min'         => 'Material name must be at least 2 characters.',
            'addBomQty.required'       => 'Quantity is required.',
            'addBomQty.min'            => 'Quantity must be greater than zero.',
            'addBomUnitPrice.required' => 'Unit price is required.',
        ]);

        $qty       = (float) $this->addBomQty;
        $unitPrice = (float) $this->addBomUnitPrice;
        $i         = $this->addBomItemIndex;

        $items = $this->items;
        if (isset($items[$i])) {
            $items[$i]['bom'][] = [
                'id'         => null,
                'name'       => trim($this->addBomSearch),
                'quantity'   => $qty,
                'unit'       => trim($this->addBomUnit),
                'unit_price' => $unitPrice,
                'line_total' => round($unitPrice * $qty, 2),
            ];
            $this->items = $items;
        }

        $this->cancelAddBom();
    }

    public function cancelAddBom(): void
    {
        $this->addBomItemIndex = -1;
        $this->addBomSearch    = '';
        $this->addBomResults   = [];
        $this->addBomProductId = null;
        $this->addBomQty       = '';
        $this->addBomUnit      = '';
        $this->addBomUnitPrice = '';
    }

    // â"€â"€ Modal BOM line removal (production modal step 4) â"€â"€â"€â"€â"€â"€â"€â"€â"€
    public function openModalBomRemove(int $bomIndex): void
    {
        $this->modalBomRemoveIndex  = $bomIndex;
        $this->modalBomRemoveReason = '';
        $this->showModalBomRemove   = true;
    }

    public function confirmModalBomRemove(): void
    {
        $this->validate(
            ['modalBomRemoveReason' => 'required|min:3'],
            ['modalBomRemoveReason.required' => 'A reason is required.', 'modalBomRemoveReason.min' => 'Reason must be at least 3 characters.']
        );

        $bi = $this->modalBomRemoveIndex;
        if (isset($this->modalBom[$bi])) {
            $line = $this->modalBom[$bi];
            $this->modalBomRemovals[] = [
                'name'       => $line['name'],
                'quantity'   => $line['quantity'],
                'unit'       => $line['unit'],
                'reason'     => trim($this->modalBomRemoveReason),
                'removed_by' => auth()->user()?->name ?? 'Staff',
                'removed_at' => now()->format('g:i A'),
            ];
            array_splice($this->modalBom, $bi, 1);
            $this->modalBom = array_values($this->modalBom);
        }

        $this->showModalBomRemove   = false;
        $this->modalBomRemoveReason = '';
        $this->modalBomRemoveIndex  = -1;
    }

    public function cancelModalBomRemove(): void
    {
        $this->showModalBomRemove   = false;
        $this->modalBomRemoveReason = '';
        $this->modalBomRemoveIndex  = -1;
    }

    // â"€â"€ Modal Add-BOM Methods â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€â"€
    public function toggleModalAddBom(): void
    {
        $this->modalAddBomOpen      = ! $this->modalAddBomOpen;
        $this->modalAddBomSearch    = '';
        $this->modalAddBomResults   = [];
        $this->modalAddBomProductId = null;
        $this->modalAddBomQty       = '';
        $this->modalAddBomUnit      = '';
        $this->modalAddBomUnitPrice = '';
    }

    public function updatedModalAddBomSearch(): void
    {
        $this->modalAddBomProductId = null;
        $this->modalAddBomUnit      = '';
        $this->modalAddBomUnitPrice = '';

        $q = trim($this->modalAddBomSearch);
        if (strlen($q) < 2) {
            $this->modalAddBomResults = [];
            return;
        }

        $this->modalAddBomResults = \App\Models\Product::where('is_material', true)
            ->where('is_active', true)
            ->where('name', 'like', "%{$q}%")
            ->limit(6)
            ->get(['id', 'name', 'unit', 'price'])
            ->toArray();
    }

    public function selectModalBomResult(int $productId): void
    {
        $product = \App\Models\Product::find($productId);
        if (! $product) return;

        $this->modalAddBomProductId = $productId;
        $this->modalAddBomSearch    = $product->name;
        $this->modalAddBomUnit      = $product->unit ?? '';
        $this->modalAddBomUnitPrice = (string) (float) $product->price;
        $this->modalAddBomResults   = [];
    }

    public function confirmModalAddBomLine(): void
    {
        $this->validate([
            'modalAddBomSearch'    => 'required|min:2',
            'modalAddBomQty'       => 'required|numeric|min:0.001',
            'modalAddBomUnitPrice' => 'required|numeric|min:0',
        ], [
            'modalAddBomSearch.required'    => 'Enter or select a material name.',
            'modalAddBomSearch.min'         => 'Name must be at least 2 characters.',
            'modalAddBomQty.required'       => 'Quantity is required.',
            'modalAddBomQty.numeric'        => 'Quantity must be a number.',
            'modalAddBomQty.min'            => 'Quantity must be greater than 0.',
            'modalAddBomUnitPrice.required' => 'Unit price is required.',
            'modalAddBomUnitPrice.numeric'  => 'Unit price must be a number.',
            'modalAddBomUnitPrice.min'      => 'Unit price cannot be negative.',
        ]);

        $qty       = (float) $this->modalAddBomQty;
        $unitPrice = (float) $this->modalAddBomUnitPrice;

        $this->modalBom[] = [
            'id'         => null,
            'name'       => trim($this->modalAddBomSearch),
            'quantity'   => $qty,
            'unit'       => trim($this->modalAddBomUnit),
            'unit_price' => $unitPrice,
            'line_total' => round($unitPrice * $qty, 2),
        ];

        $this->modalAddBomOpen      = false;
        $this->modalAddBomSearch    = '';
        $this->modalAddBomResults   = [];
        $this->modalAddBomProductId = null;
        $this->modalAddBomQty       = '';
        $this->modalAddBomUnit      = '';
        $this->modalAddBomUnitPrice = '';
    }

    public function cancelModalAddBom(): void
    {
        $this->modalAddBomOpen      = false;
        $this->modalAddBomSearch    = '';
        $this->modalAddBomResults   = [];
        $this->modalAddBomProductId = null;
        $this->modalAddBomQty       = '';
        $this->modalAddBomUnit      = '';
        $this->modalAddBomUnitPrice = '';
    }

    // ── Production Modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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
        $this->modalBomRemovals     = [];
        $this->showModalBomRemove   = false;
        $this->modalBomRemoveIndex  = -1;
        $this->modalBomRemoveReason = '';
        $this->modalAddBomOpen      = false;
        $this->modalAddBomSearch    = '';
        $this->modalAddBomResults   = [];
        $this->modalAddBomProductId = null;
        $this->modalAddBomQty       = '';
        $this->modalAddBomUnit      = '';
        $this->modalAddBomUnitPrice = '';
        $this->modalWashingRequired = true;
        $this->modalWashingSkipReason = '';
        $this->modalDeliveryType    = 'pickup';
        $this->modalDeliveryAddress = $this->customerAddress;
        $this->modalDeliveryDate    = '';
        $this->modalNotes           = '';
        $this->modalDesignNotes     = '';
        $this->modalDesignFile      = null;

        // Pre-load BOM from product materials
        $product = Product::with(['materials.material', 'measurementTemplate', 'variants' => fn ($q) => $q->where('is_active', true)->orderBy('variant_type')])->find($productId);
        if ($product) {
            $hours = (int) ($product->estimated_production_hours ?? 0);
            $days  = $hours > 0 ? (int) ceil($hours / 8) : 7;
            $this->modalDeliveryDate = now()->addWeekdays($days)->format('Y-m-d');

            foreach ($product->materials as $m) {
                $unitPrice = (float) ($m->material?->price ?? 0);
                $this->modalBom[] = [
                    'id'         => $m->id,
                    'name'       => $m->material?->name ?? '—',
                    'quantity'   => (float) $m->quantity,
                    'unit'       => $m->material?->unit ?? '',
                    'unit_price' => $unitPrice,
                    'line_total' => round($unitPrice * (float) $m->quantity, 2),
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
        $this->showProductModal     = false;
        $this->modalProductId       = null;
        $this->modalAddBomOpen      = false;
        $this->modalAddBomSearch    = '';
        $this->modalAddBomResults   = [];
        $this->modalAddBomProductId = null;
        $this->modalAddBomQty       = '';
        $this->modalAddBomUnit      = '';
        $this->modalAddBomUnitPrice = '';
    }

    public function getModalProduct(): ?Product
    {
        if (! $this->modalProductId) return null;
        return Product::with(['materials', 'measurementTemplate', 'variants' => fn ($q) => $q->where('is_active', true)->orderBy('variant_type')])->find($this->modalProductId);
    }

    // â”€â”€ Customer Modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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

    // â”€â”€ Order-step customer search â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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

        $this->customerId      = $customer->id;
        $this->customerName    = $customer->name;
        $this->customerPhone   = $customer->phone;
        $this->customerEmail   = $customer->email ?? '';
        $this->customerAddress = $customer->address ?? '';
        $this->customerSearch  = '';
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

        // Pre-fill delivery address from this customer if not already set
        if (empty(trim($this->modalDeliveryAddress)) && !empty($customer->address)) {
            $this->modalDeliveryAddress = $customer->address;
        }

        $this->prefillModalMeasurements();
    }

    public function setModalDeliveryType(string $type): void
    {
        $this->modalDeliveryType = $type;

        if ($type === 'delivery' && empty(trim($this->modalDeliveryAddress))) {
            if (!empty($this->customerAddress)) {
                $this->modalDeliveryAddress = $this->customerAddress;
            } elseif ($this->modalCustomerId) {
                $customer = Customer::find($this->modalCustomerId);
                if ($customer && !empty($customer->address)) {
                    $this->modalDeliveryAddress = $customer->address;
                }
            }
        }
    }

    private function prefillModalMeasurements(): void
    {
        if (! $this->modalCustomerId || ! $this->modalProductId) return;

        $composite = [];

        // 1. Lowest priority: any field value seen across ALL previous product measurements.
        //    This ensures even a customer with no body profile still gets their known sizes.
        \App\Models\CustomerMeasurement::where('customer_id', $this->modalCustomerId)
            ->whereNotNull('measurements')
            ->get()
            ->each(function ($m) use (&$composite) {
                foreach ((array) ($m->measurements ?? []) as $fieldId => $value) {
                    if ($value !== null && $value !== '') {
                        $composite[$fieldId] = $value;
                    }
                }
            });

        // 2. Active body measurement profile overrides the composite (more authoritative).
        $profile = \App\Models\CustomerBodyMeasurement::where('customer_id', $this->modalCustomerId)
            ->where('is_active', true)
            ->first();
        if ($profile) {
            foreach ((array) ($profile->measurements ?? []) as $fieldId => $value) {
                if ($value !== null && $value !== '') {
                    $composite[$fieldId] = $value;
                }
            }
        }

        // 3. Highest priority: measurements saved specifically for this product.
        $saved = \App\Models\CustomerMeasurement::where('customer_id', $this->modalCustomerId)
            ->where('product_id', $this->modalProductId)
            ->first();
        if ($saved) {
            foreach ((array) ($saved->measurements ?? []) as $fieldId => $value) {
                if ($value !== null && $value !== '') {
                    $composite[$fieldId] = $value;
                }
            }
        }

        if (! empty($composite)) {
            $this->modalMeasurements = $composite;
        }
    }

    public function getCustomerSavedMeasurements(): \Illuminate\Support\Collection
    {
        if (! $this->modalCustomerId) return collect();

        return \App\Models\CustomerMeasurement::with('product')
            ->where('customer_id', $this->modalCustomerId)
            ->whereNotNull('measurements')
            ->get()
            ->filter(fn ($m) => ! empty($m->measurements));
    }

    public function getCustomerBodyMeasurement(): ?\App\Models\CustomerBodyMeasurement
    {
        if (! $this->modalCustomerId) return null;

        return \App\Models\CustomerBodyMeasurement::where('customer_id', $this->modalCustomerId)
            ->where('is_active', true)
            ->latest()
            ->first();
    }

    public function loadFromSavedMeasurement(int $measurementId): void
    {
        $saved = \App\Models\CustomerMeasurement::find($measurementId);
        if (! $saved || $saved->customer_id !== $this->modalCustomerId) return;

        $this->applyMeasurementSet((array) ($saved->measurements ?? []));
        Notification::make()->title('Measurements loaded.')->success()->send();
    }

    public function loadFromBodyMeasurement(): void
    {
        $profile = \App\Models\CustomerBodyMeasurement::where('customer_id', $this->modalCustomerId)
            ->where('is_active', true)
            ->latest()
            ->first();

        if (! $profile || empty($profile->measurements)) {
            Notification::make()->title('No default profile found.')->warning()->send();
            return;
        }

        $this->applyMeasurementSet((array) ($profile->measurements ?? []));
        Notification::make()->title('Default measurements loaded.')->success()->send();
    }

    /** Merges a measurement array into the current modal fields (only fills template fields). */
    private function applyMeasurementSet(array $source): void
    {
        $product  = $this->getModalProduct();
        $fieldIds = $product?->measurementTemplate?->fields ?? [];

        $measurements = $this->modalMeasurements;
        foreach ($fieldIds as $fieldId) {
            if (isset($source[$fieldId]) && $source[$fieldId] !== '') {
                $measurements[$fieldId] = $source[$fieldId];
            }
        }
        $this->modalMeasurements = $measurements;
    }

    // Steps: 1 = customer/variant, 2 = measurements, 3 = design, 4 = confirm
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

        if ($this->modalStep === 1 && ! $hasFields) {
            $this->modalStep = 3; // skip measurements step
            return;
        }

        if ($this->modalStep === 2 && $hasFields) {
            $fieldIds = $product->measurementTemplate->fields;
            $missing  = \App\Models\MeasurementField::whereIn('id', $fieldIds)
                ->orderBy('label')
                ->get()
                ->filter(fn ($f) => ! isset($this->modalMeasurements[$f->id]) ||
                                    $this->modalMeasurements[$f->id] === '' ||
                                    $this->modalMeasurements[$f->id] === null)
                ->pluck('label');

            if ($missing->isNotEmpty()) {
                Notification::make()
                    ->title('Missing measurements')
                    ->body('Please fill in: ' . $missing->join(', ') . '.')
                    ->danger()
                    ->send();
                return;
            }
        }

        $this->modalStep = min(4, $this->modalStep + 1);
    }

    public function modalPrev(): void
    {
        $product   = $this->getModalProduct();
        $hasFields = $product && $product->measurementTemplate && ! empty($product->measurementTemplate->fields);

        if ($this->modalStep === 3 && ! $hasFields) {
            $this->modalStep = 1; // skip back over measurements
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
            'production_path_key'  => \App\Models\OrderItem::detectPath($product, $this->categoryPathKey()),
            'customer_id'          => $this->modalCustomerId,
            '_customer_name'       => $this->modalCustomerName,
            '_customer_phone'      => $this->modalCustomerPhone,
            '_customer_email'      => $this->modalCustomerEmail,
            'measurements'         => $this->modalMeasurements,
            'bom'                  => $this->modalBom,
            'bom_removals'         => $this->modalBomRemovals,
            'washing_required'     => $this->modalWashingRequired,
            'washing_skipped'      => ! $this->modalWashingRequired,
            'washing_skip_reason'  => $this->modalWashingSkipReason,
            'delivery_type'        => $this->modalDeliveryType,
            'delivery_date'        => $this->modalDeliveryDate ?: null,
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

        // Sync delivery address back to order level if delivery was chosen
        if ($this->modalDeliveryType === 'delivery' && !empty(trim($this->modalDeliveryAddress))) {
            $this->customerAddress = trim($this->modalDeliveryAddress);
        }

        $this->showProductModal = false;
        $this->modalProductId   = null;

        if (!empty($this->modalDeliveryDate)) {
            // Cashier explicitly chose a date — honour it; take max with any prior item's date
            if (empty($this->estimatedCompletionDate) || $this->modalDeliveryDate > $this->estimatedCompletionDate) {
                $this->estimatedCompletionDate = $this->modalDeliveryDate;
            }
        } else {
            // No explicit date — fall back to auto-calculation from product hours
            $this->autoFillEstimatedDate();
        }
    }

    // ── Manual Item Management â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function setDeliveryType(string $type): void
    {
        $this->deliveryType = in_array($type, ['pickup', 'delivery']) ? $type : 'pickup';
    }

    public function incrementQty(int $index): void
    {
        if (! isset($this->items[$index])) return;
        $items = $this->items;
        $items[$index]['qty'] = ($items[$index]['qty'] ?? 1) + 1;
        $price = (float) ($items[$index]['unit_price'] ?? 0);
        $items[$index]['subtotal'] = round($items[$index]['qty'] * $price, 2);
        $this->items = $items;
        $this->autoFillEstimatedDate();
    }

    public function decrementQty(int $index): void
    {
        if (! isset($this->items[$index])) return;
        $newQty = ($this->items[$index]['qty'] ?? 1) - 1;
        if ($newQty < 1) {
            $this->removeItem($index);
            return;
        }
        $items = $this->items;
        $items[$index]['qty'] = $newQty;
        $price = (float) ($items[$index]['unit_price'] ?? 0);
        $items[$index]['subtotal'] = round($newQty * $price, 2);
        $this->items = $items;
        $this->autoFillEstimatedDate();
    }

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
            'delivery_type'       => 'pickup',
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
            'delivery_type'       => 'pickup',
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

        $this->autoFillEstimatedDate();
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

        $this->autoFillEstimatedDate();
    }

    // â”€â”€ Sidebar event handlers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    #[On('pos-remove-item')]
    public function handleSidebarRemoveItem(int $index): void
    {
        $this->removeItem($index);
    }

    #[On('pos-increment-qty')]
    public function handleSidebarIncrementQty(int $index): void
    {
        $this->incrementQty($index);
    }

    #[On('pos-decrement-qty')]
    public function handleSidebarDecrementQty(int $index): void
    {
        $this->decrementQty($index);
    }

    #[On('pos-clear-cart')]
    public function handleSidebarClearCart(): void
    {
        $this->clearCart();
    }

    #[On('pos-set-delivery-type')]
    public function handleSidebarSetDeliveryType(string $type): void
    {
        $this->setDeliveryType($type);
    }

    #[On('pos-set-estimated-date')]
    public function handleSidebarSetEstimatedDate(string $date): void
    {
        $this->estimatedCompletionDate = $date;
    }

    #[On('pos-set-notes')]
    public function handleSidebarSetNotes(string $notes): void
    {
        $this->notes = $notes;
    }

    #[On('pos-open-customer-modal')]
    public function handleSidebarOpenCustomerModal(): void
    {
        $this->openCustomerModal();
    }

    #[On('pos-proceed-payment')]
    public function handleSidebarProceedPayment(): void
    {
        $this->processOrder();
    }

    #[On('pos-complete-order')]
    public function handleSidebarCompleteOrder(
        string $payMode,
        float  $cashAmt,
        float  $transferAmt,
        string $couponCode    = '',
        float  $couponDiscount = 0.0
    ): void {
        $this->sidebarCouponCode     = $couponCode;
        $this->sidebarCouponDiscount = $couponDiscount;

        $netTotal = max(0, $this->getTotal() - $couponDiscount);

        if ($payMode === 'cash') {
            $this->splits = [['method' => 'cash', 'amount' => (string) $cashAmt]];
        } elseif ($payMode === 'transfer') {
            $this->splits = [['method' => 'transfer', 'amount' => (string) $transferAmt]];
        } else {
            $this->splits = [];
            if ($cashAmt > 0) {
                $this->splits[] = ['method' => 'cash', 'amount' => (string) $cashAmt];
            }
            if ($transferAmt > 0) {
                $this->splits[] = ['method' => 'transfer', 'amount' => (string) $transferAmt];
            }
        }

        $this->completeSale();
    }

    #[On('pos-new-sale')]
    public function handleSidebarNewSale(): void
    {
        $this->newSale();
    }

    #[On('pos-add-line')]
    public function handleSidebarAddLine(): void
    {
        $this->addBlankItem();
    }

    #[On('pos-update-item')]
    public function handleSidebarUpdateItem(int $index, string $field, mixed $value): void
    {
        if (! isset($this->items[$index])) return;
        if (! in_array($field, ['description', 'unit_price', 'delivery_type'])) return;

        $items = $this->items;
        $items[$index][$field] = $value;

        if ($field === 'unit_price') {
            $qty = (int) ($items[$index]['qty'] ?? 1);
            $items[$index]['subtotal'] = round((float) $value * $qty, 2);
        }

        $this->items = $items;
    }

    // â”€â”€ Step Navigation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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
            ['phone' => Customer::normalizePhone($this->customerPhone)],
            $fillable
        );
        $this->customerId = $customer->id;

        // Pre-fill first split with the full order total so cashier only
        // needs to change it if the customer is paying differently
        if (empty(array_filter(array_column($this->splits, 'amount')))) {
            $this->splits[0]['amount'] = (string) $this->getTotal();
        }

        $this->posStep = 'payment';
        $this->dispatch('pos-step-payment');
    }

    public function backToOrder(): void
    {
        $this->posStep = 'order';
    }

    // â”€â”€ Computed â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function getTotal(): float
    {
        return round(collect($this->items)->sum(fn ($i) => (float) ($i['subtotal'] ?? 0)), 2);
    }

    public function hasProductionItems(): bool
    {
        return collect($this->items)->contains(fn ($i) => ($i['production_type'] ?? 'ready_made') === 'production');
    }

    public function autoFillEstimatedDate(): void
    {
        if (! $this->hasProductionItems()) {
            $this->estimatedCompletionDate = '';
            return;
        }

        $productIds = collect($this->items)
            ->where('production_type', 'production')
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values();

        $maxHours = 0;
        if ($productIds->isNotEmpty()) {
            $maxHours = \App\Models\Product::whereIn('id', $productIds)
                ->max('estimated_production_hours') ?? 0;
        }

        // Default to 7 days if no estimate stored; convert hours to working days (8 hrs/day)
        $days = $maxHours > 0 ? (int) ceil($maxHours / 8) : 7;

        $this->estimatedCompletionDate = now()->addWeekdays($days)->format('Y-m-d');
    }

    public function getSplitTotal(): float
    {
        return round(collect($this->splits)->sum(fn ($s) => (float) ($s['amount'] ?? 0)), 2);
    }

    public function getChange(): float
    {
        return max(0, round($this->getSplitTotal() - $this->getTotal(), 2));
    }

    public function getBalance(): float
    {
        return max(0, round($this->getTotal() - $this->getSplitTotal(), 2));
    }

    public function addSplit(): void
    {
        $this->splits[] = ['method' => 'transfer', 'amount' => ''];
    }

    public function removeSplit(int $index): void
    {
        if (count($this->splits) > 1) {
            array_splice($this->splits, $index, 1);
            $this->splits = array_values($this->splits);
        }
    }

    public function fillRemaining(int $index): void
    {
        $remaining = $this->getBalance();
        if ($remaining > 0) {
            $this->splits[$index]['amount'] = (string) $remaining;
        }
    }

    public function getCompletedOrder(): ?Order
    {
        if (! $this->completedOrderId) return null;
        return Order::with('items')->find($this->completedOrderId);
    }

    // â”€â”€ Validation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    protected function rules(): array
    {
        return [
            'customerName'        => ['required', 'string', 'min:2'],
            'customerPhone'       => ['required', 'string', 'min:7'],
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

    // â”€â”€ Complete Sale â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    private function deriveOrderDeliveryType(): string
    {
        $types = collect($this->items)
            ->filter(fn ($i) => !empty(trim($i['description'] ?? '')))
            ->pluck('delivery_type')
            ->unique()
            ->values();

        if ($types->count() === 1) return $types->first() ?? 'pickup';
        if ($types->count() > 1)  return 'mixed';
        return 'pickup';
    }

    public function completeSale(): void
    {
        $this->validate();

        $rawTotal       = $this->getTotal();
        $couponDiscount = max(0.0, $this->sidebarCouponDiscount);
        $total          = max(0.0, round($rawTotal - $couponDiscount, 2));
        $amountPaid     = $this->getSplitTotal();

        $policy  = \App\Models\AppSetting::get('payment_policy', 'half_upfront');
        $percent = max(1, (int) \App\Models\AppSetting::get('deposit_percent', 50));

        if ($policy === 'half_upfront') {
            $minDeposit = $total * ($percent / 100);
            if ($amountPaid < $minDeposit) {
                Notification::make()
                    ->title('Minimum deposit required')
                    ->body("At least {$percent}% (₦" . number_format($minDeposit, 0) . ') must be paid before confirming this order.')
                    ->danger()
                    ->send();
                return;
            }
        }

        $paymentStatus = match (true) {
            $amountPaid <= 0        => 'unpaid',
            $amountPaid < $total    => 'partial',
            default                 => 'paid',
        };

        // Customer was already saved in processOrder(); fall back to upsert if somehow missing
        $customer = $this->customerId
            ? Customer::find($this->customerId)
            : null;

        if (! $customer) {
            $fillable = ['name' => $this->customerName];
            if ($this->customerEmail)   $fillable['email']   = $this->customerEmail;
            if ($this->customerAddress) $fillable['address']  = $this->customerAddress;
            $customer = Customer::updateOrCreate(['phone' => Customer::normalizePhone($this->customerPhone)], $fillable);
        }

        $order = Order::create([
            'customer_id'      => $customer->id,
            'customer_name'    => $this->customerName,
            'customer_email'   => $this->customerEmail ?: ($customer->email ?? null),
            'customer_phone'   => $this->customerPhone,
            'customer_address' => $this->customerAddress ?: null,
            'type'             => $this->orderType,
            'status'           => 'confirmed',
            'notes'            => $this->notes ?: null,
            'total_amount'     => $total,
            'coupon_discount'  => $couponDiscount > 0 ? $couponDiscount : 0,
            'amount_paid'      => $amountPaid,
            'payment_status'   => $paymentStatus,
            'delivery_type'             => $this->deriveOrderDeliveryType(),
            'estimated_completion_date' => $this->estimatedCompletionDate ?: null,
        ]);

        foreach ($this->splits as $split) {
            $amt = (float) ($split['amount'] ?? 0);
            if ($amt > 0) {
                $order->recordPayment($amt, $split['method'], 'POS payment via ' . strtoupper($split['method']));
            }
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
                'delivery_type'       => $item['delivery_type'] ?? 'pickup',
                'delivery_date'       => $item['delivery_date'] ?? null,
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

        // Non-production delivery orders have nothing to produce â€” advance to ready now
        if (! $hasProduction) {
            $order->syncStatusFromItems();
        }

        OrderStatusLog::create([
            'order_id'      => $order->id,
            'changed_by'    => auth()->id(),
            'status'        => 'confirmed',
            'notes'         => 'Created via POS — ' . collect($this->splits)->filter(fn($s) => (float)($s['amount']??0) > 0)->map(fn($s) => strtoupper($s['method']))->join(' + '),
            'is_published'  => true,
            'client_message'=> 'Your order has been received and confirmed.',
        ]);

        // Notify customer
        try {
            app(NotificationService::class)->orderConfirmed($order);
        } catch (\Throwable) {
            // Notification failure must not block sale completion
        }

        // Apply coupon via service if one was used
        if ($this->sidebarCouponCode && $couponDiscount > 0) {
            try {
                $coupon = Coupon::where('code', $this->sidebarCouponCode)->first();
                if ($coupon) {
                    app(CouponService::class)->apply($coupon, $order, $couponDiscount, $customer);
                }
            } catch (\Throwable) {
                // Coupon recording failure must not block sale
            }
        }

        $this->clearCartSession();
        $this->sidebarCouponDiscount = 0.0;
        $this->sidebarCouponCode     = '';
        $this->completedOrderId = $order->id;
        $this->showReceipt = true;

        $this->dispatch('order-sale-done');

        Notification::make()
            ->title('Sale completed - ' . $order->reference)
            ->success()
            ->send();
    }

    public function clearCart(): void
    {
        $this->newSale();
    }

    public function newSale(): void
    {
        $this->clearCartSession();
        $this->reset([
            'customerName', 'customerPhone', 'customerEmail', 'customerAddress',
            'notes', 'items', 'completedOrderId', 'showReceipt',
            'customerId', 'estimatedCompletionDate', 'search', 'showProductModal', 'modalProductId',
            'modalDesignNotes', 'modalDesignFile', 'showCustomerModal', 'processAfterCustomer',
            'showVariantModal', 'variantModalProductId', 'variantModalSelectedId',
        ]);
        $this->orderType    = 'tailoring';
        $this->splits       = [['method' => 'cash', 'amount' => '']];
        $this->deliveryType = 'pickup';
        $this->posStep       = 'order';
        $this->addBlankItem();
        $this->dispatch('new-sale-started');
    }
}

