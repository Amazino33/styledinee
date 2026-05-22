<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('clothing_types')) return;

        Schema::create('clothing_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('measurement_field_ids');
            $table->string('unit')->default('inches');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clothing_types');
    }
};
