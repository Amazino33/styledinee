<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `products` MODIFY `product_type` ENUM('ready_made','embroidery','printing','fabric','accessory','garment') DEFAULT 'ready_made'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `products` MODIFY `product_type` ENUM('ready_made','embroidery','printing','fabric','accessory') DEFAULT 'ready_made'");
    }
};
