<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'variant_type', 'variant_value', 'price_adjustment', 'is_active'];

    protected $casts = [
        'price_adjustment' => 'decimal:2',
        'is_active'        => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function label(): string
    {
        return ucfirst($this->variant_type) . ': ' . $this->variant_value;
    }
}
