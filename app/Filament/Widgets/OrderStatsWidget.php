<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'cashier']);
    }

    protected function getStats(): array
    {
        $todayCount    = Order::whereDate('created_at', today())->count();
        $todayRevenue  = Order::whereDate('created_at', today())->sum('total_amount');
        $activeCount   = Order::whereIn('status', ['confirmed', 'in_progress'])->count();
        $readyCount    = Order::where('status', 'ready')->count();

        return [
            Stat::make('Orders Today', $todayCount)
                ->description('₦' . number_format($todayRevenue, 0) . ' in new orders')
                ->icon('heroicon-o-shopping-bag')
                ->color('primary'),

            Stat::make('Active Orders', $activeCount)
                ->description('Confirmed or in production')
                ->icon('heroicon-o-arrow-path')
                ->color('warning'),

            Stat::make('Ready for Collection', $readyCount)
                ->description('Awaiting customer pickup')
                ->icon('heroicon-o-check-badge')
                ->color('success'),
        ];
    }
}
