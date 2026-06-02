<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMaterial extends Model
{
    protected $fillable = ['product_id', 'material_id', 'quantity'];

    protected $casts = ['quantity' => 'decimal:3'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** The product that serves as a material in this BOM line. */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'material_id');
    }
}
