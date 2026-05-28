<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usernames', function (Blueprint $table) {
            $table->string('username', 50)->primary();
            $table->enum('entity_type', ['customer', 'user', 'affiliate']);
            $table->unsignedBigInteger('entity_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usernames');
    }
};
