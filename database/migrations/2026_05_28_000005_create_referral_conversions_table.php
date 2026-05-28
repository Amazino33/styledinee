<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_conversions', function (Blueprint $table) {
            $table->id();

            // Who referred — stored as username (snapshot) so we don't lose history
            // if the referrer is later deleted
            $table->string('referrer_username', 50);
            $table->enum('referrer_type', ['customer', 'user', 'affiliate']);
            $table->unsignedBigInteger('referrer_entity_id');

            $table->foreignId('referred_customer_id')->constrained('customers');
            $table->foreignId('order_id')->constrained('orders');

            $table->decimal('reward_amount', 10, 2);
            $table->enum('payout_type', ['credit', 'bank_transfer']);
            $table->enum('status', ['pending', 'credited', 'paid', 'cancelled'])->default('pending');

            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('referrer_username');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_conversions');
    }
};
