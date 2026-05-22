<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'staff_marked_done')) {
                $table->boolean('staff_marked_done')->default(false)->after('stage_updated_at');
            }
            if (! Schema::hasColumn('order_items', 'staff_done_at')) {
                $table->timestamp('staff_done_at')->nullable()->after('staff_marked_done');
            }
            if (! Schema::hasColumn('order_items', 'staff_done_by')) {
                $table->foreignId('staff_done_by')->nullable()->constrained('users')->nullOnDelete()->after('staff_done_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'staff_done_by');
            $table->dropColumn(['staff_marked_done', 'staff_done_at', 'staff_done_by']);
        });
    }
};
