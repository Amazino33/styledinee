<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new index first so the customer_id FK has a backing index before we drop the old one
        Schema::table('customer_measurements', function (Blueprint $table) {
            $table->unique(['customer_id', 'clothing_type_id']);
        });

        Schema::table('customer_measurements', function (Blueprint $table) {
            $table->dropUnique('customer_measurements_customer_id_product_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('customer_measurements', function (Blueprint $table) {
            $table->dropUnique(['customer_id', 'clothing_type_id']);
        });
    }
};
