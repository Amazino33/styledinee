<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class PromoteToAdmin extends Command
{
    protected $signature   = 'admin:promote {email} {role=super_admin}';
    protected $description = 'Assign an admin role to a user by email';

    public function handle(): int
    {
        $email = $this->argument('email');
        $role  = $this->argument('role');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No user found with email: {$email}");
            return 1;
        }

        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $user->assignRole($role);

        $this->info("✓ {$user->name} ({$email}) → role '{$role}' assigned.");
        $this->line('All roles: ' . $user->getRoleNames()->join(', '));

        return 0;
    }
}
