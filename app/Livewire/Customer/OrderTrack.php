<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderTrack extends Component
{
    public string $reference;

    public function mount(string $reference): void
    {
        $this->reference = $reference;
    }

    public function render()
    {
        $customer = Auth::guard('customer_web')->user();

        $order = Order::with([
                'items.product',
                'statusLogs' => fn ($q) => $q->where('is_published', true)->orderBy('created_at'),
            ])
            ->where('reference', $this->reference)
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                  ->orWhere('customer_phone', $customer->phone);
            })
            ->firstOrFail();

        // Build a unique, ordered list of stages across all production items
        $stages  = $this->buildPipelineStages($order);
        $current = $this->currentPipelineStage($order);

        return view('livewire.customer.order-track', compact('order', 'stages', 'current'))
            ->layout('layouts.customer')
            ->title('Order ' . $order->reference);
    }

    private function buildPipelineStages(Order $order): array
    {
        // Always start with confirmed
        $ordered = ['confirmed', 'in_progress', 'sewing', 'embroidery', 'printing', 'finishing', 'ready'];

        if ($order->delivery_type === 'delivery') {
            $ordered[] = 'dispatched';
            $ordered[] = 'delivered';
        }

        $labels = [
            'confirmed'   => 'Confirmed',
            'in_progress' => 'In Progress',
            'sewing'      => 'Sewing',
            'embroidery'  => 'Embroidery',
            'printing'    => 'Printing',
            'finishing'   => 'Finishing',
            'ready'       => 'Ready',
            'dispatched'  => 'Dispatched',
            'delivered'   => 'Delivered',
        ];

        // Determine which stages are relevant for this order's items
        $itemStages = collect();
        foreach ($order->items as $item) {
            if (! empty($item->production_path)) {
                $itemStages = $itemStages->merge($item->production_path);
            }
        }

        $relevant = ['confirmed'];
        if ($itemStages->isNotEmpty()) {
            foreach (['sewing', 'embroidery', 'printing', 'finishing'] as $s) {
                if ($itemStages->contains($s)) $relevant[] = $s;
            }
        } else {
            $relevant[] = 'in_progress';
        }
        $relevant[] = 'ready';
        if ($order->delivery_type === 'delivery') {
            $relevant[] = 'dispatched';
            $relevant[] = 'delivered';
        }

        $result = [];
        foreach (array_unique($relevant) as $s) {
            $result[] = ['key' => $s, 'label' => $labels[$s] ?? ucfirst($s)];
        }

        return $result;
    }

    private function currentPipelineStage(Order $order): string
    {
        // Map order status to pipeline stage key
        return match($order->status) {
            'confirmed'   => 'confirmed',
            'in_progress' => 'in_progress',
            'ready'       => 'ready',
            'dispatched'  => 'dispatched',
            'delivered'   => 'delivered',
            'cancelled'   => 'cancelled',
            default       => 'confirmed',
        };
    }
}
