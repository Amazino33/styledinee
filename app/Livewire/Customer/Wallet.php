<?php

namespace App\Livewire\Customer;

use App\Models\ReferralCreditLedger;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Wallet extends Component
{
    public function render()
    {
        $customer = Auth::guard('customer_web')->user();
        $username = $customer->username;

        $balance = $customer->creditBalance();

        $transactions = $username
            ? ReferralCreditLedger::where('owner_username', $username)
                ->orderByDesc('created_at')
                ->limit(50)
                ->get()
            : collect();

        return view('livewire.customer.wallet', [
            'customer'     => $customer,
            'balance'      => $balance,
            'transactions' => $transactions,
        ])->layout('layouts.customer')->title('Wallet');
    }
}
