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
            ['email' => 'amazino33@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('change-this-password'),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('admin');

        $this->command->info("Admin user ready: {$user->email}");
    }
}
