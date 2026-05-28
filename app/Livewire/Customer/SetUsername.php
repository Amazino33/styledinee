<?php

namespace App\Livewire\Customer;

use App\Models\Username;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SetUsername extends Component
{
    public string $name     = '';
    public string $username = '';
    public ?string $error   = null;

    public function mount(): void
    {
        $customer   = Auth::guard('customer_web')->user();
        $this->name = ($customer->name && $customer->name !== 'Customer') ? $customer->name : '';
    }

    public function save(): void
    {
        $this->validate([
            'username' => [
                'required', 'string', 'max:50', 'alpha_dash',
                'unique:customers,username',
                'unique:users,username',
                'unique:affiliates,username',
            ],
        ]);

        $this->error = null;

        if (! Username::isAvailable($this->username)) {
            $this->error = 'That username is already taken.';
            return;
        }

        $customer = Auth::guard('customer_web')->user();
        $name     = $this->name ?: $customer->name;

        DB::transaction(function () use ($customer, $name) {
            $customer->update([
                'name'     => $name,
                'username' => $this->username,
            ]);
            Username::claim($this->username, 'customer', $customer->id);
        });

        $this->redirect(route('account.dashboard'));
    }

    public function render()
    {
        return view('livewire.customer.set-username')
            ->layout('layouts.customer')
            ->title('Choose a Username');
    }
}
