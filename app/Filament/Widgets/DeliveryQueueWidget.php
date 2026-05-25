<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\Widget;

class DeliveryQueueWidget extends Widget
{
    protected string $view = 'filament.widgets.delivery-queue-widget';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('delivery');
    }

    protected function getViewData(): array
    {
        $orders = Order::with('customer')
            ->where('delivery_user_id', auth()->id())
            ->whereIn('status', ['ready', 'in_progress'])
            ->latest('updated_at')
            ->get();

        return ['orders' => $orders];
    }
}
