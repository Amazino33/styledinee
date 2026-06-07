<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Admin: every permission ───────────────────────────────��───────────
        $admin = Role::findOrCreate('admin');
        $admin->syncPermissions(Permission::all());

        // ── Cashier ───────────────────────────────────────────────────────────
        $cashier = Role::findOrCreate('cashier');
        $cashier->syncPermissions([
            // Orders (no delete)
            'ViewAny:Order', 'View:Order', 'Create:Order', 'Update:Order',

            // Customers (full)
            'ViewAny:Customer', 'View:Customer', 'Create:Customer',
            'Update:Customer', 'Delete:Customer', 'DeleteAny:Customer',

            // Enquiries (view + edit, no create or delete)
            'ViewAny:Enquiry', 'View:Enquiry', 'Update:Enquiry',

            // Services (full)
            'ViewAny:Service', 'View:Service', 'Create:Service',
            'Update:Service', 'Delete:Service', 'DeleteAny:Service',

            // Gallery (full)
            'ViewAny:Gallery', 'View:Gallery', 'Create:Gallery',
            'Update:Gallery', 'Delete:Gallery', 'DeleteAny:Gallery',

            // Pages
            'View:Pos',
            'View:CashierHistory',
            'View:DeliveryHistory',
            'View:EndOfDay',
            'View:ProductionTracker',
            'View:TransactionHistory',
        ]);

        // ── Tailor ────────────────────────────────────────────────────────────
        $tailor = Role::findOrCreate('tailor');
        $tailor->syncPermissions([
            'View:TailorQueue',
            'View:StaffHistory',
        ]);

        // ── Dry Cleaner ───────────────────────────────────────────────────────
        $dryCleaner = Role::findOrCreate('dry_cleaner');
        $dryCleaner->syncPermissions([
            'View:WashingQueue',
            'View:FinishingQueue',
            'View:StaffHistory',
        ]);

        // ── Delivery ──────────────────────────────────────────────────────────
        $delivery = Role::findOrCreate('delivery');
        $delivery->syncPermissions([
            'View:DeliveryQueue',
            'View:StaffHistory',
        ]);

        // ── Embroidery ────────────────────────────────────────────────────────
        $embroidery = Role::findOrCreate('embroidery');
        $embroidery->syncPermissions([
            'View:EmbroideryQueue',
            'View:StaffHistory',
        ]);

        // ── Printer ───────────────────────────────────────────────────────────
        $printer = Role::findOrCreate('printer');
        $printer->syncPermissions([
            'View:PrintingQueue',
            'View:StaffHistory',
        ]);
    }
}
