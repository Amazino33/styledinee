<?php

namespace App\Filament\Widgets;

use App\Models\OrderAssignment;
use Filament\Widgets\Widget;

class RecentWorkWidget extends Widget
{
    protected string $view = 'filament.widgets.recent-work-widget';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['tailor', 'embroidery', 'printer', 'dry_cleaner', 'delivery']);
    }

    protected function getViewData(): array
    {
        $recentWork = OrderAssignment::with(['order.customer', 'orderItem.product'])
            ->where('assigned_to', auth()->id())
            ->where('status', 'complete')
            ->latest('completed_at')
            ->limit(8)
            ->get();

        $totalThisMonth = OrderAssignment::where('assigned_to', auth()->id())
            ->where('status', 'complete')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->count();

        $totalThisWeek = OrderAssignment::where('assigned_to', auth()->id())
            ->where('status', 'complete')
            ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return compact('recentWork', 'totalThisMonth', 'totalThisWeek');
    }
}
