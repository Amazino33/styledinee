<?php

namespace App\Livewire\Customer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Profile extends Component
{
    public string $name    = '';
    public string $email   = '';
    public string $address = '';
    public string $username = '';

    public string $current_password        = '';
    public string $new_password            = '';
    public string $new_password_confirmation = '';

    public bool $saved          = false;
    public bool $passwordSaved  = false;
    public ?string $passwordError = null;

    public function mount(): void
    {
        $customer        = Auth::guard('customer_web')->user();
        $this->name      = $customer->name ?? '';
        $this->email     = $customer->email ?? '';
        $this->address   = $customer->address ?? '';
        $this->username  = $customer->username ?? '';
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

    public function changePassword(): void
    {
        $this->passwordError  = null;
        $this->passwordSaved  = false;

        $this->validate([
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $customer = Auth::guard('customer_web')->user();

        // If they already have a password, require the current one
        if ($customer->password) {
            if (empty($this->current_password) || ! Hash::check($this->current_password, $customer->password)) {
                $this->passwordError = 'Current password is incorrect.';
                return;
            }
        }

        $customer->update(['password' => $this->new_password]);

        $this->current_password         = '';
        $this->new_password             = '';
        $this->new_password_confirmation = '';
        $this->passwordSaved            = true;
    }

    public function render()
    {
        $customer = Auth::guard('customer_web')->user();

        return view('livewire.customer.profile', [
            'hasPassword' => (bool) $customer->password,
        ])->layout('layouts.customer')->title('Profile');
    }
}
