<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add is_material + unit to products
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_material')->default(false)->after('is_published');
            $table->string('unit', 50)->nullable()->after('is_material')
                ->comment('Unit of measure when this product is used as a material (e.g. metres, pieces)');
        });

        // 2. Migrate each material → a product row with is_material = true
        $materialToProductId = [];

        $materials = DB::table('materials')->get();
        foreach ($materials as $mat) {
            $productId = DB::table('products')->insertGetId([
                'name'            => $mat->name,
                'slug'            => 'material-' . \Illuminate\Support\Str::slug($mat->name) . '-' . $mat->id,
                'description'     => $mat->description,
                'price'           => 0,
                'stock_quantity'  => (int) $mat->stock_quantity,
                'is_active'       => $mat->is_active,
                'is_published'    => false,
                'is_material'     => true,
                'unit'            => $mat->unit,
                'production_type' => 'ready_made',
                'product_type'    => 'accessory',
                'sort_order'      => 0,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
            $materialToProductId[$mat->id] = $productId;
        }

        // 3. Re-point product_materials.material_id → products.id
        Schema::table('product_materials', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
        });

        foreach ($materialToProductId as $oldMaterialId => $newProductId) {
            DB::table('product_materials')
                ->where('material_id', $oldMaterialId)
                ->update(['material_id' => $newProductId]);
        }

        Schema::table('product_materials', function (Blueprint $table) {
            $table->foreign('material_id')->references('id')->on('products')->cascadeOnDelete();
        });

        // 4. Drop the old materials table
        Schema::dropIfExists('materials');
    }

    public function down(): void
    {
        // Reverse: recreate materials table, move data back
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

        Schema::table('product_materials', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_material', 'unit']);
        });
    }
};
