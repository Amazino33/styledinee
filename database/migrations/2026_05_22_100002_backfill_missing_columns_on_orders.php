<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('orders', 'estimated_completion_date')) {
                $table->date('estimated_completion_date')->nullable();
            }
            if (! Schema::hasColumn('orders', 'delivery_type')) {
                $table->string('delivery_type')->nullable();
            }
            if (! Schema::hasColumn('orders', 'delivery_notes')) {
                $table->text('delivery_notes')->nullable();
            }
            if (! Schema::hasColumn('orders', 'delivery_user_id')) {
                $table->foreignId('delivery_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['delivery_user_id']);
            $table->dropColumn(['customer_id', 'estimated_completion_date', 'delivery_type', 'delivery_notes', 'delivery_user_id']);
        });
    }
};
