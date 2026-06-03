<?php

namespace App\Filament\Widgets;

use App\Models\Order;
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
        $isDriver = auth()->user()?->hasRole('delivery');
        $userId   = auth()->id();

        if ($isDriver) {
            $recentWork = Order::with('customer')
                ->where('delivery_user_id', $userId)
                ->where('status', 'delivered')
                ->latest('updated_at')
                ->limit(8)
                ->get()
                ->map(fn ($order) => (object) [
                    'type'        => 'delivery',
                    'label'       => 'Delivery',
                    'description' => $order->customer_name,
                    'sub'         => $order->reference,
                    'completed_at'=> $order->updated_at,
                ]);

            $totalThisWeek = Order::where('delivery_user_id', $userId)
                ->where('status', 'delivered')
                ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count();

            $totalThisMonth = Order::where('delivery_user_id', $userId)
                ->where('status', 'delivered')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count();
        } else {
            $assignments = OrderAssignment::with(['order.customer', 'orderItem.product'])
                ->where('assigned_to', $userId)
                ->where('status', 'complete')
                ->latest('completed_at')
                ->limit(8)
                ->get();

            $stageColors = [
                'tailor'      => ['label' => 'Sewing',      'text' => '#6366f1'],
                'sewing'      => ['label' => 'Sewing',      'text' => '#6366f1'],
                'embroidery'  => ['label' => 'Embroidery',  'text' => '#a855f7'],
                'printing'    => ['label' => 'Printing',    'text' => '#3b82f6'],
                'printer'     => ['label' => 'Printing',    'text' => '#3b82f6'],
                'finishing'   => ['label' => 'Finishing',   'text' => '#d97706'],
                'dry_cleaner' => ['label' => 'Washing',     'text' => '#14b8a6'],
                'washing'     => ['label' => 'Washing',     'text' => '#14b8a6'],
            ];

            $recentWork = $assignments->map(function ($a) use ($stageColors) {
                $dept  = $a->department ?? $a->orderItem?->item_stage ?? 'sewing';
                $color = $stageColors[$dept] ?? ['label' => ucfirst($dept), 'text' => '#6b7280'];
                return (object) [
                    'type'        => 'assignment',
                    'label'       => $color['label'],
                    'color'       => $color['text'],
                    'description' => $a->orderItem?->description ?? '—',
                    'sub'         => ($a->order?->reference ?? '—')
                                   . ($a->order?->customer ? ' · ' . $a->order->customer->name : ''),
                    'completed_at'=> $a->completed_at,
                ];
            });

            $totalThisWeek = OrderAssignment::where('assigned_to', $userId)
                ->where('status', 'complete')
                ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count();

            $totalThisMonth = OrderAssignment::where('assigned_to', $userId)
                ->where('status', 'complete')
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->count();
        }

        return compact('recentWork', 'totalThisWeek', 'totalThisMonth', 'isDriver');
    }
}
