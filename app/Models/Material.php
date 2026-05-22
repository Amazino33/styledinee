<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    protected $fillable = [
        'name', 'unit', 'stock_quantity', 'low_stock_threshold', 'description', 'is_active',
    ];

    protected $casts = [
        'stock_quantity'      => 'decimal:3',
        'low_stock_threshold' => 'decimal:3',
        'is_active'           => 'boolean',
    ];

    public function productMaterials(): HasMany
    {
        return $this->hasMany(ProductMaterial::class);
    }

    public function isLowStock(): bool
    {
        return (float) $this->stock_quantity <= (float) $this->low_stock_threshold;
    }
}
