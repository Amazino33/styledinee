<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'gallery';

    protected $fillable = [
        'title', 'description', 'image', 'category', 'sections', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sections'  => 'array',
    ];

    public function scopeForSection($query, string $section)
    {
        return $query->where('is_active', true)
            ->whereJsonContains('sections', $section)
            ->inRandomOrder();
    }
}
