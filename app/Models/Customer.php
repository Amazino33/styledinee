<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'address',
    ];

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => self::normalizePhone($value),
        );
    }

    public static function normalizePhone(string $phone): string
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(CustomerMeasurement::class);
    }

    public function measurementForProduct(int $productId): ?CustomerMeasurement
    {
        return $this->measurements()->where('product_id', $productId)->first();
    }
}
