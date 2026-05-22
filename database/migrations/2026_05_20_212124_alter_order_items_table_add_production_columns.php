<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('order_items', 'production_type')) {
                $table->string('production_type')->nullable()->after('service_id');
            }
            if (! Schema::hasColumn('order_items', 'item_stage')) {
                $table->string('item_stage')->nullable()->after('production_type');
            }
            if (! Schema::hasColumn('order_items', 'measurements')) {
                $table->json('measurements')->nullable()->after('item_stage');
            }
            if (! Schema::hasColumn('order_items', 'washing_required')) {
                $table->boolean('washing_required')->default(false)->after('measurements');
            }
            if (! Schema::hasColumn('order_items', 'washing_skipped')) {
                $table->boolean('washing_skipped')->default(false)->after('washing_required');
            }
            if (! Schema::hasColumn('order_items', 'washing_skip_reason')) {
                $table->string('washing_skip_reason')->nullable()->after('washing_skipped');
            }
            if (! Schema::hasColumn('order_items', 'stage_updated_at')) {
                $table->timestamp('stage_updated_at')->nullable()->after('washing_skip_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn([
                'customer_id', 'production_type', 'item_stage',
                'measurements', 'washing_required', 'washing_skipped',
                'washing_skip_reason', 'stage_updated_at',
            ]);
        });
    }
};
