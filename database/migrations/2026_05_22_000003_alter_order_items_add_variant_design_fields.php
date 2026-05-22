<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'variant_id')) {
                $table->foreignId('variant_id')->nullable()->after('product_id')->constrained('product_variants')->nullOnDelete();
            }
            if (! Schema::hasColumn('order_items', 'design_notes')) {
                $table->text('design_notes')->nullable()->after('measurements');
            }
            if (! Schema::hasColumn('order_items', 'design_file')) {
                $table->string('design_file')->nullable()->after('design_notes');
            }
            if (! Schema::hasColumn('order_items', 'production_notes')) {
                $table->text('production_notes')->nullable()->after('design_file');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\ProductVariant::class, 'variant_id');
            $table->dropColumn(['variant_id', 'design_notes', 'design_file', 'production_notes']);
        });
    }
};
