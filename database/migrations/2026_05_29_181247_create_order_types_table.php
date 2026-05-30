<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->boolean('needs_production')->default(true);
            $table->boolean('needs_measurements')->default(false);
            $table->boolean('needs_estimated_date')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('order_types')->insert([
            ['name' => 'Tailoring',         'slug' => 'tailoring',       'icon' => '🧵', 'needs_production' => true,  'needs_measurements' => true,  'needs_estimated_date' => true,  'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Alteration',        'slug' => 'alteration',      'icon' => '✂️', 'needs_production' => true,  'needs_measurements' => false, 'needs_estimated_date' => true,  'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ready-Made',        'slug' => 'ready_made',      'icon' => '👗', 'needs_production' => false, 'needs_measurements' => false, 'needs_estimated_date' => false, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dry Cleaning',      'slug' => 'dry_cleaning',    'icon' => '🧹', 'needs_production' => false, 'needs_measurements' => false, 'needs_estimated_date' => true,  'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pickup & Delivery', 'slug' => 'pickup_delivery', 'icon' => '🚚', 'needs_production' => false, 'needs_measurements' => false, 'needs_estimated_date' => false, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('order_type_id')->nullable()->after('type')->constrained('order_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_type_id');
        });
        Schema::dropIfExists('order_types');
    }
};
