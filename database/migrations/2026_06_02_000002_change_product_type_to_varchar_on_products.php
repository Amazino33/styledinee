<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ENUM is too restrictive now that product_type is derived from
        // free-form category slugs. Change to VARCHAR so any slug is valid.
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_type', 100)->default('ready_made')->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('product_type', [
                'ready_made', 'embroidery', 'printing', 'fabric', 'accessory',
            ])->default('ready_made')->change();
        });
    }
};
