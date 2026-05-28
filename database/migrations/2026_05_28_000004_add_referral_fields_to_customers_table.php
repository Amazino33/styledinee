<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique()->after('name');

            // Set at signup from the referral code entered — never changes
            $table->string('referred_by_username', 50)->nullable()->after('username');

            // The affiliate earning % on this customer's orders — admin can edit
            $table->foreignId('affiliate_id')->nullable()->after('referred_by_username')
                ->constrained('affiliates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['affiliate_id']);
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'referred_by_username', 'affiliate_id']);
        });
    }
};
