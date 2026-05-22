<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'address',
    ];

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
