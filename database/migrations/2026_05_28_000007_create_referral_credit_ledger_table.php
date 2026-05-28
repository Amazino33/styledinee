<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Immutable ledger — never update rows, only insert.
        // Balance = SUM(credit entries) - SUM(debit entries) per owner_username.
        Schema::create('referral_credit_ledger', function (Blueprint $table) {
            $table->id();

            $table->string('owner_username', 50);
            $table->enum('owner_type', ['customer', 'user', 'affiliate']);
            $table->unsignedBigInteger('owner_entity_id');

            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 10, 2);
            $table->string('description');

            // Links back to the source of this entry
            $table->string('reference_type')->nullable(); // e.g. 'referral_conversion', 'order'
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->timestamps();

            $table->index('owner_username');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_credit_ledger');
    }
};
