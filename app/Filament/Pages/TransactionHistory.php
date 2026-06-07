<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Payment;
use Filament\Pages\Page;
use Livewire\WithPagination;

class TransactionHistory extends Page
{
    use WithPagination;

    protected string $view = 'filament.pages.transaction-history';
    protected static ?string $navigationLabel = 'Transactions';
    protected static ?string $title = 'Transaction History';
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string { return 'heroicon-o-banknotes'; }
    public static function getNavigationGroup(): ?string { return 'Finance'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:TransactionHistory') ?? false;
    }

    public string $search       = '';
    public string $methodFilter = '';
    public string $dateFrom     = '';
    public string $dateTo       = '';

    protected $queryString = ['search', 'methodFilter', 'dateFrom', 'dateTo'];

    public function updatedSearch(): void    { $this->resetPage(); }
    public function updatedMethodFilter(): void { $this->resetPage(); }
    public function updatedDateFrom(): void  { $this->resetPage(); }
    public function updatedDateTo(): void    { $this->resetPage(); }

    public function getPayments()
    {
        $query = Payment::with(['order', 'recordedBy'])
            ->latest();

        if ($this->search) {
            $term = '%' . $this->search . '%';
            $query->whereHas('order', fn ($q) =>
                $q->where('reference', 'like', $term)
                  ->orWhere('customer_name', 'like', $term)
                  ->orWhere('customer_phone', 'like', $term)
            );
        }

        if ($this->methodFilter) {
            $query->where('method', $this->methodFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query->paginate(50);
    }

    public function getSummary(): array
    {
        $base = Payment::query();

        $todayBase = (clone $base)->whereDate('created_at', today());

        return [
            'total_collected'   => (float) Payment::sum('amount'),
            'today_collected'   => (float) $todayBase->sum('amount'),
            'total_outstanding' => (float) Order::whereIn('payment_status', ['unpaid', 'partial'])
                                              ->selectRaw('SUM(total_amount - amount_paid)')
                                              ->value('SUM(total_amount - amount_paid)') ?? 0,
            'partial_count'     => Order::where('payment_status', 'partial')->count(),
        ];
    }
}

