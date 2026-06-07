<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Filament\Pages\Page;
use Livewire\WithPagination;

class DeliveryHistory extends Page
{
    use WithPagination;

    protected string $view = 'filament.pages.delivery-history';
    protected static ?string $navigationLabel = 'Delivery History';
    protected static ?string $title           = 'Delivery History';
    protected static ?int    $navigationSort  = 12;

    public static function getNavigationIcon(): string   { return 'heroicon-o-truck'; }
    public static function getNavigationGroup(): ?string { return 'Orders'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:DeliveryHistory') ?? false;
    }

    public string $search     = '';
    public string $dateFrom   = '';
    public string $dateTo     = '';
    public string $sortCol    = 'updated_at';
    public string $sortDir    = 'desc';
    public ?int   $driverId   = null; // admin-only filter

    protected $queryString = ['search', 'dateFrom', 'dateTo', 'sortCol', 'sortDir', 'driverId'];

    public function updatedSearch(): void   { $this->resetPage(); }
    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void   { $this->resetPage(); }
    public function updatedDriverId(): void { $this->resetPage(); }

    public function sortBy(string $column): void
    {
        if ($this->sortCol === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortCol = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function isAdmin(): bool
    {
        return (bool) auth()->user()?->hasRole('admin');
    }

    public function getDriverList(): array
    {
        return User::role('delivery')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getStats(): array
    {
        $base = Order::where('status', 'delivered')->where('delivery_type', 'delivery');

        if (! $this->isAdmin()) {
            $base->where('delivery_user_id', auth()->id());
        } elseif ($this->driverId) {
            $base->where('delivery_user_id', $this->driverId);
        }

        $cashBase = Payment::where('method', 'cash');
        if (! $this->isAdmin()) {
            $cashBase->where('recorded_by', auth()->id());
        } elseif ($this->driverId) {
            $cashBase->where('recorded_by', $this->driverId);
        }

        return [
            'total'      => (clone $base)->count(),
            'today'      => (clone $base)->whereDate('updated_at', today())->count(),
            'this_week'  => (clone $base)
                ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'cash_collected' => $cashBase->sum('amount'),
        ];
    }

    public function getHistory()
    {
        $allowed = ['updated_at', 'total_amount', 'customer_name'];
        $col     = in_array($this->sortCol, $allowed, true) ? $this->sortCol : 'updated_at';
        $dir     = $this->sortDir === 'asc' ? 'asc' : 'desc';

        $query = Order::with(['deliveryUser'])
            ->where('status', 'delivered')
            ->where('delivery_type', 'delivery')
            ->orderBy($col, $dir);

        if (! $this->isAdmin()) {
            $query->where('delivery_user_id', auth()->id());
        } elseif ($this->driverId) {
            $query->where('delivery_user_id', $this->driverId);
        }

        if ($this->search) {
            $term = '%' . $this->search . '%';
            $query->where(fn ($q) =>
                $q->where('reference', 'like', $term)
                  ->orWhere('customer_name', 'like', $term)
                  ->orWhere('customer_phone', 'like', $term)
                  ->orWhere('customer_address', 'like', $term)
            );
        }

        if ($this->dateFrom) {
            $query->whereDate('updated_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('updated_at', '<=', $this->dateTo);
        }

        return $query->paginate(20);
    }
}
