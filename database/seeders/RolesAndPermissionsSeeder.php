<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Services
            'view services', 'create services', 'edit services', 'delete services',
            // Products
            'view products', 'create products', 'edit products', 'delete products',
            // Orders
            'view orders', 'create orders', 'edit orders', 'delete orders',
            'update order status', 'manage payments',
            // Gallery
            'view gallery', 'create gallery', 'edit gallery', 'delete gallery',
            // Enquiries
            'view enquiries', 'respond enquiries',
            // Users / Roles
            'manage users', 'manage roles',
            // Status Logs
            'view status logs',
            // Customers
            'view customers', 'create customers', 'edit customers', 'delete customers',
            // Production workflow
            'manage assignments', 'verify delivery otp', 'manage bom',
            'skip washing', 'view tailor queue', 'view washing queue', 'view delivery queue',
            'view printing queue',
            // Client portal (used outside admin panel)
            'view own orders',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $cashier = Role::firstOrCreate(['name' => 'cashier']);
        $cashier->syncPermissions([
            'view services', 'view products',
            'view orders', 'create orders', 'edit orders',
            'manage payments', 'update order status',
            'view enquiries', 'respond enquiries',
            'view gallery',
            'view customers', 'create customers', 'edit customers',
            'skip washing',
        ]);

        $tailor = Role::firstOrCreate(['name' => 'tailor']);
        $tailor->syncPermissions([
            'view orders', 'update order status', 'view tailor queue',
        ]);

        $dry_cleaner = Role::firstOrCreate(['name' => 'dry_cleaner']);
        $dry_cleaner->syncPermissions([
            'view orders', 'update order status', 'view washing queue',
        ]);

        $delivery = Role::firstOrCreate(['name' => 'delivery']);
        $delivery->syncPermissions([
            'view orders', 'update order status', 'view delivery queue', 'verify delivery otp',
        ]);

        $printer = Role::firstOrCreate(['name' => 'printer']);
        $printer->syncPermissions([
            'view orders', 'update order status', 'view printing queue',
        ]);

        $client = Role::firstOrCreate(['name' => 'client']);
        $client->syncPermissions(['view own orders']);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
