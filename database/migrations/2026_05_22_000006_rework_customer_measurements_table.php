<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('customer_measurements', 'product_id')) {
            // Drop unique constraint first (MySQL blocks column drop while index exists)
            if ($this->indexExists('customer_measurements_customer_id_product_id_unique')) {
                Schema::table('customer_measurements', function (Blueprint $table) {
                    $table->dropUnique('customer_measurements_customer_id_product_id_unique');
                });
            }
            // Drop FK separately
            if ($this->indexExists('customer_measurements_product_id_foreign')) {
                Schema::table('customer_measurements', function (Blueprint $table) {
                    $table->dropForeign(['product_id']);
                });
            }
            // Now safe to drop the columns
            Schema::table('customer_measurements', function (Blueprint $table) {
                $table->dropColumn(
                    array_filter(['product_id', Schema::hasColumn('customer_measurements', 'values') ? 'values' : null])
                );
            });
        }

        Schema::table('customer_measurements', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_measurements', 'clothing_type_id')) {
                $table->foreignId('clothing_type_id')->constrained()->cascadeOnDelete();
            }
            if (! Schema::hasColumn('customer_measurements', 'measurements')) {
                $table->json('measurements');
            }
            if (! Schema::hasColumn('customer_measurements', 'unit')) {
                $table->string('unit')->default('inches');
            }
            if (! Schema::hasColumn('customer_measurements', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    private function indexExists(string $index): bool
    {
        $indexes = \Illuminate\Support\Facades\DB::select(
            "SHOW INDEX FROM `customer_measurements` WHERE Key_name = ?", [$index]
        );
        return ! empty($indexes);
    }

    public function down(): void
    {
        Schema::table('customer_measurements', function (Blueprint $table) {
            $table->dropForeign(['clothing_type_id']);
            $table->dropColumn(['clothing_type_id', 'measurements', 'unit', 'notes']);

            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->json('values')->nullable();
        });
    }
};
