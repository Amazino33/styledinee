<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialsInventory extends Model
{
    protected $fillable = [
        'product_material_id', 'stock_quantity', 'low_stock_threshold',
    ];

    protected $casts = [
        'stock_quantity'      => 'decimal:3',
        'low_stock_threshold' => 'decimal:3',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(ProductMaterial::class, 'product_material_id');
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }
}
