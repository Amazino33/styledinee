<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'amazino33@styledinee.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'is_active' => true
            ]
        );

        $user->assignRole('super_admin');

        $this->command->info("Admin user ready: {$user->email}");

        $staff = User::firstOrCreate(
            ['email' => 'staff@styledinee.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $staff->assignRole('cashier');
        $this->command->info("Staff user ready: {$staff->email}");
    }
}
