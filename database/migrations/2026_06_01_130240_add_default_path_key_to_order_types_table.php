<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_types', function (Blueprint $table) {
            $table->string('default_path_key')->default('none')->after('needs_estimated_date')
                ->comment('Default OrderItem::PATHS key for items in this category');
        });

        // Seed sensible defaults for existing categories
        DB::table('order_types')->where('slug', 'tailoring')->update(['default_path_key' => 'sewing_only']);
        DB::table('order_types')->where('slug', 'alteration')->update(['default_path_key' => 'sewing_only']);
        DB::table('order_types')->where('slug', 'ready_made')->update(['default_path_key' => 'none']);
        DB::table('order_types')->where('slug', 'dry_cleaning')->update(['default_path_key' => 'none']);
        DB::table('order_types')->where('slug', 'pickup_delivery')->update(['default_path_key' => 'none']);
    }

    public function down(): void
    {
        Schema::table('order_types', function (Blueprint $table) {
            $table->dropColumn('default_path_key');
        });
    }
};
