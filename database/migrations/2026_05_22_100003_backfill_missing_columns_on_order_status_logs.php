<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_status_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('order_status_logs', 'order_item_id')) {
                $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('order_status_logs', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable();
            }
            if (! Schema::hasColumn('order_status_logs', 'is_published')) {
                $table->boolean('is_published')->default(true);
            }
            if (! Schema::hasColumn('order_status_logs', 'client_message')) {
                $table->text('client_message')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_status_logs', function (Blueprint $table) {
            $table->dropForeign(['order_item_id']);
            $table->dropColumn(['order_item_id', 'scheduled_at', 'is_published', 'client_message']);
        });
    }
};
