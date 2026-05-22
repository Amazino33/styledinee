<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove clothing_type_id if it still exists (may already be gone from partial run)
        if (Schema::hasColumn('customer_measurements', 'clothing_type_id')) {
            // Need a backing index for customer_id FK before dropping the unique
            if (! $this->indexExists('customer_measurements', 'cm_customer_id_tmp')) {
                Schema::table('customer_measurements', function (Blueprint $table) {
                    $table->index('customer_id', 'cm_customer_id_tmp');
                });
            }

            Schema::table('customer_measurements', function (Blueprint $table) {
                if ($this->indexExists('customer_measurements', 'customer_measurements_customer_id_clothing_type_id_unique')) {
                    $table->dropUnique('customer_measurements_customer_id_clothing_type_id_unique');
                }
                if ($this->indexExists('customer_measurements', 'customer_measurements_clothing_type_id_foreign')) {
                    $table->dropIndex('customer_measurements_clothing_type_id_foreign');
                }
                $table->dropColumn('clothing_type_id');
            });
        }

        // Add product_id if not yet present
        if (! Schema::hasColumn('customer_measurements', 'product_id')) {
            \Illuminate\Support\Facades\DB::table('customer_measurements')->truncate();

            Schema::table('customer_measurements', function (Blueprint $table) {
                $table->foreignId('product_id')->after('customer_id')->constrained()->cascadeOnDelete();
            });
        }

        // Add unique constraint if missing
        if (! $this->indexExists('customer_measurements', 'customer_measurements_customer_id_product_id_unique')) {
            Schema::table('customer_measurements', function (Blueprint $table) {
                $table->unique(['customer_id', 'product_id']);
            });
        }

        // Add FK on product_id if missing
        if (! $this->fkExists('customer_measurements', 'customer_measurements_product_id_foreign')) {
            Schema::table('customer_measurements', function (Blueprint $table) {
                $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            });
        }

        // Drop the temporary backing index now that the unique (customer_id, product_id) covers it
        if ($this->indexExists('customer_measurements', 'cm_customer_id_tmp')) {
            Schema::table('customer_measurements', function (Blueprint $table) {
                $table->dropIndex('cm_customer_id_tmp');
            });
        }
    }

    public function down(): void
    {
        Schema::table('customer_measurements', function (Blueprint $table) {
            $table->dropUnique(['customer_id', 'product_id']);
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');

            $table->foreignId('clothing_type_id')->after('customer_id')->constrained()->cascadeOnDelete();
            $table->unique(['customer_id', 'clothing_type_id']);
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
        return ! empty($indexes);
    }

    private function fkExists(string $table, string $fk): bool
    {
        $result = \Illuminate\Support\Facades\DB::select(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$table, $fk]
        );
        return ! empty($result);
    }
};
