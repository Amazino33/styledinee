<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = fn(string $name) => ! empty(\Illuminate\Support\Facades\DB::select(
            "SHOW INDEX FROM `customer_measurements` WHERE Key_name = ?", [$name]
        ));

        if (! $indexes('customer_measurements_customer_id_clothing_type_id_unique')) {
            Schema::table('customer_measurements', function (Blueprint $table) {
                $table->unique(['customer_id', 'clothing_type_id']);
            });
        }

        if ($indexes('customer_measurements_customer_id_product_id_unique')) {
            Schema::table('customer_measurements', function (Blueprint $table) {
                $table->dropUnique('customer_measurements_customer_id_product_id_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('customer_measurements', function (Blueprint $table) {
            $table->dropUnique(['customer_id', 'clothing_type_id']);
        });
    }
};
