<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // GET /api/orders
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user('customer');

        $orders = Order::with(['items.product', 'statusLogs' => fn ($q) => $q->where('is_published', true)->latest()])
            ->where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($order) => $this->formatOrder($order, brief: true));

        return response()->json(['data' => $orders]);
    }

    // GET /api/orders/{reference}
    public function show(Request $request, string $reference): JsonResponse
    {
        $customer = $request->user('customer');

        $order = Order::with([
            'items.product',
            'items.variant',
            'statusLogs' => fn ($q) => $q->where('is_published', true)->latest(),
            'payments',
        ])
            ->where('reference', $reference)
            ->where('customer_id', $customer->id)
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        return response()->json(['data' => $this->formatOrder($order, brief: false)]);
    }

    private function formatOrder(Order $order, bool $brief): array
    {
        $data = [
            'reference'      => $order->reference,
            'status'         => $order->status,
            'status_label'   => ucwords(str_replace('_', ' ', $order->status)),
            'type'           => $order->type,
            'delivery_type'  => $order->delivery_type,
            'total_amount'   => (float) $order->total_amount,
            'amount_paid'    => (float) $order->amount_paid,
            'payment_status' => $order->payment_status,
            'created_at'     => $order->created_at->toISOString(),
            'estimated_completion_date' => $order->estimated_completion_date?->toDateString(),
        ];

        if (! $brief) {
            $data['items'] = $order->items->map(fn ($item) => [
                'description' => $item->description,
                'quantity'    => $item->quantity,
                'unit_price'  => (float) $item->unit_price,
                'subtotal'    => (float) $item->subtotal,
                'stage'       => $item->item_stage,
                'stage_label' => ucwords(str_replace('_', ' ', $item->item_stage ?? 'pending')),
            ]);

            $data['status_updates'] = $order->statusLogs->map(fn ($log) => [
                'status'  => $log->status,
                'message' => $log->client_message ?? ucwords(str_replace('_', ' ', $log->status)),
                'date'    => $log->created_at->toISOString(),
            ]);

            $data['payments'] = $order->payments->map(fn ($p) => [
                'amount' => (float) $p->amount,
                'method' => $p->method,
                'date'   => $p->created_at->toDateString(),
            ]);
        } else {
            $data['latest_update'] = $order->statusLogs->first()?->client_message
                ?? ucwords(str_replace('_', ' ', $order->status));
        }

        return $data;
    }
}
