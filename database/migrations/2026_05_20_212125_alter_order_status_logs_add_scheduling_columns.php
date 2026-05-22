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
        // Columns already exist in create_order_status_logs_table migration — no-op
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_status_logs', function (Blueprint $table) {
            $table->dropForeign(['order_item_id']);
            $table->dropColumn(['order_item_id', 'scheduled_at', 'is_published', 'client_message']);
        });
    }
};
