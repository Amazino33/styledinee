<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Already applied by background migration process — columns are present
        Schema::table('customer_measurements', function (Blueprint $table) {
            if (Schema::hasColumn('customer_measurements', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn(['product_id', 'values']);
            }
            if (! Schema::hasColumn('customer_measurements', 'clothing_type_id')) {
                $table->foreignId('clothing_type_id')->after('customer_id')->constrained()->cascadeOnDelete();
            }
            if (! Schema::hasColumn('customer_measurements', 'measurements')) {
                $table->json('measurements')->after('clothing_type_id');
            }
            if (! Schema::hasColumn('customer_measurements', 'unit')) {
                $table->string('unit')->default('inches')->after('measurements');
            }
            if (! Schema::hasColumn('customer_measurements', 'notes')) {
                $table->text('notes')->nullable()->after('unit');
            }
        });
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
