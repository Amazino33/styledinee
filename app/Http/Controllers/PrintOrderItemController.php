<?php

namespace App\Http\Controllers;

use App\Models\MeasurementField;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class PrintOrderItemController extends Controller
{
    public function __invoke(Request $request, int $id)
    {
        $item = OrderItem::with(['order.customer', 'variant', 'product'])->findOrFail($id);

        $user = $request->user();
        abort_unless(
            $user?->hasAnyRole(['admin', 'cashier', 'tailor', 'embroidery', 'printer', 'dry_cleaner']),
            403
        );

        $measurements = collect(is_array($item->measurements) ? $item->measurements : [])
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->map(fn ($value, $fieldId) => [
                'label' => MeasurementField::find($fieldId)?->label ?? "Field #{$fieldId}",
                'value' => $value,
            ])
            ->values();

        return view('print.order-item', [
            'item'         => $item,
            'order'        => $item->order,
            'measurements' => $measurements,
        ]);
    }
}
