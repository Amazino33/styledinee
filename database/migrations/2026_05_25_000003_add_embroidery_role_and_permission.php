<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $perm = Permission::firstOrCreate(['name' => 'view embroidery queue', 'guard_name' => 'web']);

        $role = Role::firstOrCreate(['name' => 'embroidery', 'guard_name' => 'web']);
        $role->syncPermissions([
            Permission::where('name', 'view orders')->first(),
            Permission::where('name', 'update order status')->first(),
            $perm,
        ]);

        // Give admin and super_admin the new permission too
        foreach (['admin', 'super_admin'] as $roleName) {
            $r = Role::where('name', $roleName)->first();
            if ($r && ! $r->hasPermissionTo('view embroidery queue')) {
                $r->givePermissionTo($perm);
            }
        }
    }

    public function down(): void
    {
        $role = Role::where('name', 'embroidery')->first();
        $role?->delete();

        Permission::where('name', 'view embroidery queue')->delete();
    }
};
