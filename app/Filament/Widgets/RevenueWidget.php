<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    protected function getStats(): array
    {
        $monthRevenue  = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $monthCollected = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount_paid');

        $outstanding = Order::whereIn('payment_status', ['unpaid', 'partial'])
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('SUM(total_amount - amount_paid) as balance')
            ->value('balance') ?? 0;

        $totalCustomers = Customer::count();

        return [
            Stat::make('Revenue This Month', '₦' . number_format($monthRevenue, 0))
                ->description('Total billed in ' . now()->format('F'))
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Collected This Month', '₦' . number_format($monthCollected, 0))
                ->description('Payments received')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary'),

            Stat::make('Outstanding Balance', '₦' . number_format($outstanding, 0))
                ->description('Across unpaid & partial orders')
                ->icon('heroicon-o-exclamation-circle')
                ->color($outstanding > 0 ? 'danger' : 'success'),

            Stat::make('Total Customers', $totalCustomers)
                ->description('All time')
                ->icon('heroicon-o-users')
                ->color('gray'),
        ];
    }
}
