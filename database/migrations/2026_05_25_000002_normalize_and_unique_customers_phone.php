<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize existing phone numbers to +234... format
        DB::table('customers')->orderBy('id')->each(function ($customer) {
            $normalized = $this->normalize($customer->phone);
            if ($normalized !== $customer->phone) {
                DB::table('customers')->where('id', $customer->id)->update(['phone' => $normalized]);
            }
        });

        // Add unique constraint
        Schema::table('customers', function (Blueprint $table) {
            if (! $this->indexExists('customers_phone_unique')) {
                $table->unique('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['phone']);
        });
    }

    private function normalize(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '234')) {
            return '+' . $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '+234' . substr($digits, 1);
        }

        return '+' . $digits;
    }

    private function indexExists(string $index): bool
    {
        return ! empty(DB::select("SHOW INDEX FROM `customers` WHERE Key_name = ?", [$index]));
    }
};
