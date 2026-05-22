<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('materials_inventory');
        \DB::table('product_materials')->delete();

        Schema::table('product_materials', function (Blueprint $table) {
            if (! Schema::hasColumn('product_materials', 'material_id')) {
                $table->foreignId('material_id')->after('product_id')->constrained('materials')->cascadeOnDelete();
            }
            if (Schema::hasColumn('product_materials', 'name'))            $table->dropColumn('name');
            if (Schema::hasColumn('product_materials', 'unit'))            $table->dropColumn('unit');
            if (Schema::hasColumn('product_materials', 'track_inventory')) $table->dropColumn('track_inventory');
        });
    }

    public function down(): void
    {
        Schema::table('product_materials', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
            $table->dropColumn('material_id');
            $table->string('name');
            $table->string('unit')->default('piece');
            $table->boolean('track_inventory')->default(false);
        });
    }
};
