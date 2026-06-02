<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_types', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->foreign('parent_id')->references('id')->on('order_types')->nullOnDelete();

            // Null means "inherit from parent"; existing top-level rows keep their values.
            $table->boolean('needs_production')->nullable()->default(null)->change();
            $table->boolean('needs_measurements')->nullable()->default(null)->change();
            $table->boolean('needs_estimated_date')->nullable()->default(null)->change();
            $table->string('default_path_key')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_types', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');

            $table->boolean('needs_production')->nullable(false)->default(true)->change();
            $table->boolean('needs_measurements')->nullable(false)->default(false)->change();
            $table->boolean('needs_estimated_date')->nullable(false)->default(true)->change();
            $table->string('default_path_key')->nullable(false)->default('none')->change();
        });
    }
};
