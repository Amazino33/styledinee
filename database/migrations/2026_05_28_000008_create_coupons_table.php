<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();

            $table->enum('type', ['fixed', 'percentage']);
            $table->decimal('amount', 10, 2);
            $table->decimal('max_discount_amount', 10, 2)->nullable()
                ->comment('Cap for percentage coupons');
            $table->decimal('min_order_amount', 10, 2)->nullable();

            $table->unsignedInteger('usage_limit')->nullable()
                ->comment('Total redemptions allowed; null = unlimited');
            $table->unsignedInteger('usage_limit_per_customer')->nullable();
            $table->unsignedInteger('used_count')->default(0);

            // Eligibility
            $table->enum('eligibility_rule', [
                'public',
                'first_order',
                'return_customer',
                'long_time_purchaser',
                'exclusive',
            ])->default('public');
            $table->unsignedSmallInteger('eligibility_months')->nullable()
                ->comment('Only for long_time_purchaser: months since first order');

            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
