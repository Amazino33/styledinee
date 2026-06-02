<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'stock_quantity',
        'category', 'image', 'is_active', 'is_published', 'is_material', 'unit', 'sort_order',
        'production_type', 'product_type', 'order_type_id',
        'estimated_production_hours', 'is_embroidery',
    ];

    protected $casts = [
        'is_active'                  => 'boolean',
        'is_published'               => 'boolean',
        'is_material'                => 'boolean',
        'is_embroidery'              => 'boolean',
        'price'                      => 'decimal:2',
        'estimated_production_hours' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function orderType()
    {
        return $this->belongsTo(OrderType::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function measurementTemplate()
    {
        return $this->hasOne(MeasurementTemplate::class);
    }

    public function materials()
    {
        return $this->hasMany(ProductMaterial::class);
    }

    public function requiresProduction(): bool
    {
        return $this->production_type === 'production';
    }
}
