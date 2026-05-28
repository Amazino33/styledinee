<?php

namespace App\Livewire\Customer;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public string $name    = '';
    public string $email   = '';
    public string $address = '';
    public string $username = '';

    public bool $saved = false;

    public function mount(): void
    {
        $customer       = Auth::guard('customer_web')->user();
        $this->name     = $customer->name ?? '';
        $this->email    = $customer->email ?? '';
        $this->address  = $customer->address ?? '';
        $this->username = $customer->username ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $customer = Auth::guard('customer_web')->user();

        $customer->update([
            'name'    => $this->name,
            'email'   => $this->email ?: null,
            'address' => $this->address ?: null,
        ]);

        $this->saved = true;
    }

    public function render()
    {
        return view('livewire.customer.profile')
            ->layout('layouts.customer')->title('Profile');
    }
}
