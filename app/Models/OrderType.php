<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderType extends Model
{
    protected $fillable = [
        'parent_id', 'name', 'slug', 'icon',
        'needs_production', 'needs_measurements', 'needs_estimated_date',
        'default_path_key', 'is_active', 'sort_order',
    ];

    // NOTE: needs_production, needs_measurements, needs_estimated_date are intentionally
    // NOT cast to boolean here — they are nullable in the DB (null = inherit from parent).
    // Use the effective_* accessors for resolved values.
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    public function parent(): BelongsTo
    {
        return $this->belongsTo(OrderType::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(OrderType::class, 'parent_id')->orderBy('sort_order');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    public function isSubcategory(): bool
    {
        return $this->parent_id !== null;
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Returns this category's ID plus all descendant IDs at any depth.
     * Eager-load 'children' recursively before calling to avoid N+1.
     */
    public function selfAndDescendantIds(): array
    {
        $ids = [$this->id];
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->selfAndDescendantIds());
        }
        return $ids;
    }

    /**
     * Returns the ancestor chain from root down to this node, inclusive.
     * Each entry is an OrderType instance. Root is index 0.
     */
    public function ancestorChain(): array
    {
        $chain = [];
        $node  = $this->loadMissing('parent.parent.parent.parent');
        while ($node) {
            array_unshift($chain, $node);
            $node = $node->parent;
        }
        return $chain;
    }

    // ── Effective attribute accessors (inherit from parent when own value is null) ──

    public function getEffectiveNeedsProductionAttribute(): bool
    {
        $val = $this->attributes['needs_production'] ?? null;
        if ($val !== null) return (bool) $val;
        return $this->parent ? $this->parent->effective_needs_production : true;
    }

    public function getEffectiveNeedsMeasurementsAttribute(): bool
    {
        $val = $this->attributes['needs_measurements'] ?? null;
        if ($val !== null) return (bool) $val;
        return $this->parent ? $this->parent->effective_needs_measurements : false;
    }

    public function getEffectiveNeedsEstimatedDateAttribute(): bool
    {
        $val = $this->attributes['needs_estimated_date'] ?? null;
        if ($val !== null) return (bool) $val;
        return $this->parent ? $this->parent->effective_needs_estimated_date : true;
    }

    public function getEffectiveDefaultPathKeyAttribute(): string
    {
        $val = $this->attributes['default_path_key'] ?? null;
        if ($val !== null) return $val;
        return $this->parent ? $this->parent->effective_default_path_key : 'none';
    }

    // ── Tree utilities ────────────────────────────────────────────────────

    /**
     * Returns a flat ordered list of all active categories suitable for a <select>.
     * Entries deeper in the tree are indented with em-dashes.
     * Pass $excludeIds to omit a node and all its descendants (e.g. when editing).
     */
    public static function flatTreeOptions(array $excludeIds = []): array
    {
        $all = static::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('id');

        $options = [];
        $visited = [];

        $walk = function (OrderType $node, int $depth) use (
            &$walk, &$options, &$visited, $excludeIds, $all
        ): void {
            if (isset($visited[$node->id])) return;
            if (in_array($node->id, $excludeIds, true)) return;
            $visited[$node->id] = true;

            $prefix         = $depth > 0 ? str_repeat('— ', $depth) : '';
            $options[$node->id] = $prefix . $node->name;

            foreach ($all->where('parent_id', $node->id)->sortBy('sort_order') as $child) {
                $walk($child, $depth + 1);
            }
        };

        foreach ($all->whereNull('parent_id')->sortBy('sort_order') as $root) {
            $walk($root, 0);
        }

        return $options;
    }

    /**
     * Returns all root categories (no parent) with their full children tree eager-loaded.
     */
    public static function rootsWithDescendants()
    {
        return static::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with('children.children.children.children')
            ->get();
    }

    public static function active()
    {
        return static::where('is_active', true)->orderBy('sort_order')->get();
    }
}
