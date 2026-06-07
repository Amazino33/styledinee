<?php

namespace App\Filament\Pages;

use App\Models\Payment;
use App\Models\User;
use Filament\Pages\Page;
use Livewire\WithPagination;

class CashierHistory extends Page
{
    use WithPagination;

    protected string $view = 'filament.pages.cashier-history';
    protected static ?string $navigationLabel = 'Cashier History';
    protected static ?string $title           = 'Cashier Activity History';
    protected static ?int    $navigationSort  = 11;

    public static function getNavigationIcon(): string   { return 'heroicon-o-banknotes'; }
    public static function getNavigationGroup(): ?string { return 'Finance'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:CashierHistory') ?? false;
    }

    public string $search    = '';
    public string $method    = '';
    public string $dateFrom  = '';
    public string $dateTo    = '';
    public string $sortCol   = 'created_at';
    public string $sortDir   = 'desc';
    public ?int   $cashierId = null; // admin-only filter

    protected $queryString = ['search', 'method', 'dateFrom', 'dateTo', 'sortCol', 'sortDir', 'cashierId'];

    public function updatedSearch(): void    { $this->resetPage(); }
    public function updatedMethod(): void    { $this->resetPage(); }
    public function updatedDateFrom(): void  { $this->resetPage(); }
    public function updatedDateTo(): void    { $this->resetPage(); }
    public function updatedCashierId(): void { $this->resetPage(); }

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

    public function getCashierList(): array
    {
        return User::role(['cashier', 'admin'])
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getStats(): array
    {
        $base = Payment::query();

        if (! $this->isAdmin()) {
            $base->where('recorded_by', auth()->id());
        } elseif ($this->cashierId) {
            $base->where('recorded_by', $this->cashierId);
        }

        return [
            'total_collected' => (clone $base)->sum('amount'),
            'today'           => (clone $base)->whereDate('created_at', today())->sum('amount'),
            'this_week'       => (clone $base)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('amount'),
            'total_count'     => (clone $base)->count(),
        ];
    }

    public function getHistory()
    {
        $allowed = ['created_at', 'amount', 'method'];
        $col     = in_array($this->sortCol, $allowed, true) ? $this->sortCol : 'created_at';
        $dir     = $this->sortDir === 'asc' ? 'asc' : 'desc';

        $query = Payment::with(['order.customer', 'recordedBy'])
            ->orderBy($col, $dir);

        if (! $this->isAdmin()) {
            $query->where('recorded_by', auth()->id());
        } elseif ($this->cashierId) {
            $query->where('recorded_by', $this->cashierId);
        }

        if ($this->search) {
            $term = '%' . $this->search . '%';
            $query->whereHas('order', fn ($q) =>
                $q->where('reference', 'like', $term)
                  ->orWhere('customer_name', 'like', $term)
                  ->orWhere('customer_phone', 'like', $term)
            );
        }

        if ($this->method) {
            $query->where('method', $this->method);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query->paginate(20);
    }
}
