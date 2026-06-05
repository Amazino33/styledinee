<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->foreignId('closed_by')->constrained('users');
            $table->decimal('total_cash_expected', 10, 2)->default(0);
            $table->decimal('total_cash_counted',  10, 2)->default(0);
            $table->decimal('total_transfers',      10, 2)->default(0);
            $table->decimal('total_card',           10, 2)->default(0);
            $table->decimal('total_pos',            10, 2)->default(0);
            $table->decimal('total_all',            10, 2)->default(0);
            $table->decimal('discrepancy',          10, 2)->default(0);
            $table->unsignedInteger('outstanding_orders_count')->default(0);
            $table->unsignedInteger('pending_driver_cash_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['date', 'closed_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reconciliations');
    }
};
