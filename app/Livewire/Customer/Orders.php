<?php

namespace App\Livewire\Customer;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Orders extends Component
{
    public ?int $expandedId = null;

    public function toggle(int $id): void
    {
        $this->expandedId = $this->expandedId === $id ? null : $id;
    }

    public function render()
    {
        $customer = Auth::guard('customer_web')->user();

        $orders = $customer->orders()
            ->with(['items', 'statusLogs' => fn ($q) => $q->where('is_published', true)->latest()])
            ->latest()
            ->get();

        return view('livewire.customer.orders', [
            'orders' => $orders,
        ])->layout('layouts.customer')->title('My Orders');
    }
}
