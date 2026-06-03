<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use Filament\Widgets\Widget;

class CashierActivityWidget extends Widget
{
    protected string $view = 'filament.widgets.cashier-activity-widget';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['cashier', 'admin']);
    }

    protected function getViewData(): array
    {
        $userId = auth()->id();

        $recentPayments = Payment::with(['order.customer', 'recordedBy'])
            ->where('recorded_by', $userId)
            ->latest()
            ->limit(8)
            ->get();

        $todayRevenue = Payment::where('recorded_by', $userId)
            ->whereDate('created_at', today())
            ->sum('amount');

        $todayOrders = Order::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        $weekRevenue = Payment::where('recorded_by', $userId)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('amount');

        return compact('recentPayments', 'todayRevenue', 'todayOrders', 'weekRevenue');
    }
}
