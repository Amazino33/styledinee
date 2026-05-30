<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->boolean('auto_apply')->default(false)->after('is_active');
            // criteria: 'min_order_amount' already exists; add order_count threshold
            $table->unsignedInteger('auto_apply_min_orders')->default(0)->after('auto_apply')
                ->comment('Auto-apply when customer has placed at least N orders (0 = any)');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['auto_apply', 'auto_apply_min_orders']);
        });
    }
};
