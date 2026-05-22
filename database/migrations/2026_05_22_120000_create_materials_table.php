<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('materials')) return;

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit')->default('piece');
            $table->decimal('stock_quantity', 10, 3)->default(0);
            $table->decimal('low_stock_threshold', 10, 3)->default(5);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
