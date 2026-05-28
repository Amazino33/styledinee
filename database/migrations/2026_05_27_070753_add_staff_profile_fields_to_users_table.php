<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Contact
            $table->string('phone')->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');

            // Personal
            $table->date('date_of_birth')->nullable()->after('address');
            $table->enum('gender', ['male', 'female'])->nullable()->after('date_of_birth');

            // Employment
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'freelance'])
                ->default('full_time')->after('gender');
            $table->date('date_joined')->nullable()->after('employment_type');
            $table->boolean('is_active')->default(true)->after('date_joined');

            // Salary
            $table->enum('salary_type', ['monthly', 'weekly', 'per_piece'])
                ->default('monthly')->after('is_active');
            $table->decimal('salary_amount', 10, 2)->default(0)->after('salary_type');
            $table->decimal('per_piece_rate', 10, 2)->nullable()->after('salary_amount');
            $table->tinyInteger('payment_day')->nullable()->after('per_piece_rate');

            // Banking
            $table->string('bank_name')->nullable()->after('payment_day');
            $table->string('account_number')->nullable()->after('bank_name');
            $table->string('account_name')->nullable()->after('account_number');

            // Emergency
            $table->string('emergency_contact_name')->nullable()->after('account_name');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'address', 'date_of_birth', 'gender',
                'employment_type', 'date_joined', 'is_active',
                'salary_type', 'salary_amount', 'per_piece_rate', 'payment_day',
                'bank_name', 'account_number', 'account_name',
                'emergency_contact_name', 'emergency_contact_phone',
            ]);
        });
    }
};
