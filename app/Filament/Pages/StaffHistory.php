<?php

namespace App\Filament\Pages;

use App\Models\OrderAssignment;
use Filament\Pages\Page;
use Livewire\WithPagination;

class StaffHistory extends Page
{
    use WithPagination;

    protected string $view = 'filament.pages.staff-history';
    protected static ?string $navigationLabel = 'My History';
    protected static ?string $title           = 'Work History';
    protected static ?int    $navigationSort  = 10;

    public static function getNavigationIcon(): string  { return 'heroicon-o-clock'; }
    public static function getNavigationGroup(): ?string { return 'Production'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole([
            'admin', 'tailor', 'embroidery', 'printer', 'dry_cleaner', 'delivery',
        ]);
    }

    public string $search   = '';
    public string $dept     = '';
    public string $dateFrom = '';
    public string $dateTo   = '';

    protected $queryString = ['search', 'dept', 'dateFrom', 'dateTo'];

    public function updatedSearch(): void   { $this->resetPage(); }
    public function updatedDept(): void     { $this->resetPage(); }
    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void   { $this->resetPage(); }

    public function getStats(): array
    {
        $userId = auth()->id();

        $base = OrderAssignment::where('assigned_to', $userId)->where('status', 'complete');

        return [
            'total'       => $base->count(),
            'this_week'   => (clone $base)
                ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'this_month'  => (clone $base)
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->count(),
            'avg_per_day' => rescue(function () use ($base) {
                $oldest = (clone $base)->oldest('completed_at')->value('completed_at');
                if (! $oldest) return 0;
                $days = max(1, now()->diffInDays($oldest));
                return round((clone $base)->count() / $days, 1);
            }, 0),
        ];
    }

    public function getHistory()
    {
        $query = OrderAssignment::with(['order.customer', 'orderItem.product'])
            ->where('assigned_to', auth()->id())
            ->where('status', 'complete')
            ->latest('completed_at');

        if ($this->search) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('order', fn ($q2) =>
                    $q2->where('reference', 'like', $term)
                       ->orWhere('customer_name', 'like', $term)
                );
                $q->orWhereHas('orderItem', fn ($q2) =>
                    $q2->where('description', 'like', $term)
                );
            });
        }

        if ($this->dept) {
            $query->where('department', $this->dept);
        }

        if ($this->dateFrom) {
            $query->whereDate('completed_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('completed_at', '<=', $this->dateTo);
        }

        return $query->paginate(20);
    }
}
