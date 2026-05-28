<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('payment_status')
                ->constrained('coupons')->nullOnDelete();
            $table->decimal('coupon_discount', 10, 2)->default(0)->after('coupon_id');
            $table->decimal('referral_credit_used', 10, 2)->default(0)->after('coupon_discount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'coupon_discount', 'referral_credit_used']);
        });
    }
};
