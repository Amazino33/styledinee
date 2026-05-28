<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Optional links to existing entities
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Commission program settings
            $table->decimal('commission_rate', 5, 2)->nullable()
                ->comment('% override; null = use global affiliate_default_rate setting');

            // Payout preferences
            $table->enum('referral_payout_type', ['credit', 'bank_transfer'])->default('credit');
            $table->enum('affiliate_payout_type', ['credit', 'bank_transfer'])->default('bank_transfer');

            // Bank details for transfers
            $table->string('bank_name')->nullable();
            $table->string('account_number', 20)->nullable();
            $table->string('account_name')->nullable();

            // Approval workflow
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
