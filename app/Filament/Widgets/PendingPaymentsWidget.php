<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendingPaymentsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'cashier']);
    }

    protected function getStats(): array
    {
        $unpaidOrders  = Order::where('payment_status', 'unpaid')
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('COUNT(*) as cnt, SUM(total_amount) as total')
            ->first();

        $partialOrders = Order::where('payment_status', 'partial')
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('COUNT(*) as cnt, SUM(total_amount - amount_paid) as balance')
            ->first();

        return [
            Stat::make('Unpaid Orders', $unpaidOrders->cnt ?? 0)
                ->description('₦' . number_format($unpaidOrders->total ?? 0, 0) . ' total value')
                ->icon('heroicon-o-x-circle')
                ->color(($unpaidOrders->cnt ?? 0) > 0 ? 'danger' : 'success'),

            Stat::make('Partially Paid', $partialOrders->cnt ?? 0)
                ->description('₦' . number_format($partialOrders->balance ?? 0, 0) . ' still owed')
                ->icon('heroicon-o-clock')
                ->color(($partialOrders->cnt ?? 0) > 0 ? 'warning' : 'success'),
        ];
    }
}
