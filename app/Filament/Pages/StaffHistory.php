<?php

namespace App\Filament\Pages;

use App\Models\OrderAssignment;
use App\Models\User;
use Filament\Pages\Page;
use Livewire\WithPagination;

class StaffHistory extends Page
{
    use WithPagination;

    protected string $view = 'filament.pages.staff-history';
    protected static ?string $navigationLabel = 'My History';
    protected static ?string $title           = 'Work History';
    protected static ?int    $navigationSort  = 10;

    public static function getNavigationIcon(): string   { return 'heroicon-o-clock'; }
    public static function getNavigationGroup(): ?string { return 'Production'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole([
            'admin', 'tailor', 'embroidery', 'printer', 'dry_cleaner', 'delivery',
        ]);
    }

    public string  $search    = '';
    public string  $dept      = '';
    public string  $dateFrom  = '';
    public string  $dateTo    = '';
    public string  $sortCol   = 'completed_at';
    public string  $sortDir   = 'desc';
    public ?int    $staffId   = null; // admin-only filter

    protected $queryString = ['search', 'dept', 'dateFrom', 'dateTo', 'sortCol', 'sortDir', 'staffId'];

    public function updatedSearch(): void   { $this->resetPage(); }
    public function updatedDept(): void     { $this->resetPage(); }
    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void   { $this->resetPage(); }
    public function updatedStaffId(): void  { $this->resetPage(); }

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

    public function getStaffList(): array
    {
        return User::role(['tailor', 'embroidery', 'printer', 'dry_cleaner', 'delivery'])
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getStats(): array
    {
        $base = OrderAssignment::where('status', 'complete');

        if (! $this->isAdmin()) {
            $base->where('assigned_to', auth()->id());
        } elseif ($this->staffId) {
            $base->where('assigned_to', $this->staffId);
        }

        return [
            'total'       => (clone $base)->count(),
            'this_week'   => (clone $base)
                ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'this_month'  => (clone $base)
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->count(),
            'total_value' => (clone $base)
                ->join('order_items', 'order_items.id', '=', 'order_assignments.order_item_id')
                ->sum('order_items.subtotal'),
        ];
    }

    public function getHistory()
    {
        $allowed = ['completed_at', 'assigned_at', 'department', 'subtotal'];
        $col     = in_array($this->sortCol, $allowed, true) ? $this->sortCol : 'completed_at';
        $dir     = $this->sortDir === 'asc' ? 'asc' : 'desc';

        $query = OrderAssignment::with(['order.customer', 'orderItem.product', 'assignedTo'])
            ->select('order_assignments.*')
            ->leftJoin('order_items', 'order_items.id', '=', 'order_assignments.order_item_id')
            ->where('order_assignments.status', 'complete');

        // Scope: staff see only their own; admins see all (or filtered by staffId)
        if (! $this->isAdmin()) {
            $query->where('order_assignments.assigned_to', auth()->id());
        } elseif ($this->staffId) {
            $query->where('order_assignments.assigned_to', $this->staffId);
        }

        // Search
        if ($this->search) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('order', fn ($q2) =>
                    $q2->where('reference', 'like', $term)
                       ->orWhere('customer_name', 'like', $term)
                );
                $q->orWhere('order_items.description', 'like', $term);
            });
        }

        if ($this->dept) {
            $query->where('order_assignments.department', $this->dept);
        }

        if ($this->dateFrom) {
            $query->whereDate('order_assignments.completed_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('order_assignments.completed_at', '<=', $this->dateTo);
        }

        // Sorting
        if ($col === 'subtotal') {
            $query->orderBy('order_items.subtotal', $dir);
        } else {
            $query->orderBy("order_assignments.{$col}", $dir);
        }

        return $query->paginate(20);
    }
}
