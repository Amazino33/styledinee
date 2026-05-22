<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'production_type')) {
                $table->string('production_type')->nullable();
            }
            if (! Schema::hasColumn('products', 'estimated_production_hours')) {
                $table->unsignedSmallInteger('estimated_production_hours')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['production_type', 'estimated_production_hours']);
        });
    }
};
