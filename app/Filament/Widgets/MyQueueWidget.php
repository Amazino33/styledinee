<?php

namespace App\Filament\Widgets;

use App\Models\OrderAssignment;
use Filament\Widgets\Widget;

class MyQueueWidget extends Widget
{
    protected string $view = 'filament.widgets.my-queue-widget';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['tailor', 'embroidery', 'printer', 'dry_cleaner']);
    }

    protected function getViewData(): array
    {
        $assignments = OrderAssignment::with([
            'order.customer',
            'orderItem.product',
            'orderItem.variant',
        ])
            ->where('assigned_to', auth()->id())
            ->where('status', '!=', 'complete')
            ->latest('assigned_at')
            ->get();

        return ['assignments' => $assignments];
    }
}
