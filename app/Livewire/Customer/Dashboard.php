<?php

namespace App\Livewire\Customer;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $customer = Auth::guard('customer_web')->user();

        $activeOrders = $customer->orders()
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->count();

        $recentOrders = $customer->orders()
            ->with('items')
            ->latest()
            ->limit(3)
            ->get();

        return view('livewire.customer.dashboard', [
            'customer'     => $customer,
            'activeOrders' => $activeOrders,
            'totalOrders'  => $customer->orders()->count(),
            'walletBalance' => $customer->creditBalance(),
            'recentOrders' => $recentOrders,
        ])->layout('layouts.customer')->title('Dashboard');
    }
}
