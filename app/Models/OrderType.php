<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderType extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon',
        'needs_production', 'needs_measurements', 'needs_estimated_date',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'needs_production'     => 'boolean',
        'needs_measurements'   => 'boolean',
        'needs_estimated_date' => 'boolean',
        'is_active'            => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public static function active()
    {
        return static::where('is_active', true)->orderBy('sort_order')->get();
    }
}
