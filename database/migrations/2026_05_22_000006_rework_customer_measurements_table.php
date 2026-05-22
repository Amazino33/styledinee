<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('customer_measurements', 'product_id')) {
            // Add standalone index on customer_id so MySQL will allow dropping the unique (customer_id, product_id)
            if (! $this->indexExists('cm_rework_customer_id_tmp')) {
                Schema::table('customer_measurements', function (Blueprint $table) {
                    $table->index('customer_id', 'cm_rework_customer_id_tmp');
                });
            }
            // Now drop the unique constraint
            if ($this->indexExists('customer_measurements_customer_id_product_id_unique')) {
                Schema::table('customer_measurements', function (Blueprint $table) {
                    $table->dropUnique('customer_measurements_customer_id_product_id_unique');
                });
            }
            // Disable FK checks so we don't need to know the exact constraint name
            Schema::disableForeignKeyConstraints();
            Schema::table('customer_measurements', function (Blueprint $table) {
                $cols = ['product_id'];
                if (Schema::hasColumn('customer_measurements', 'values')) {
                    $cols[] = 'values';
                }
                $table->dropColumn($cols);
            });
            Schema::enableForeignKeyConstraints();
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

        // Add unique(customer_id, clothing_type_id) so it backs the customer_id FK,
        // then we can safely drop the temporary standalone index
        if (! $this->indexExists('customer_measurements_customer_id_clothing_type_id_unique')) {
            Schema::table('customer_measurements', function (Blueprint $table) {
                $table->unique(['customer_id', 'clothing_type_id']);
            });
        }
        if ($this->indexExists('cm_rework_customer_id_tmp')) {
            Schema::table('customer_measurements', function (Blueprint $table) {
                $table->dropIndex('cm_rework_customer_id_tmp');
            });
        }
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
