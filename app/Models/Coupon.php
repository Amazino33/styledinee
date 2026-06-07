<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'name', 'description',
        'type', 'amount', 'max_discount_amount', 'min_order_amount',
        'usage_limit', 'usage_limit_per_customer', 'used_count',
        'eligibility_rule', 'eligibility_months',
        'is_active', 'auto_apply', 'auto_apply_min_orders',
        'starts_at', 'expires_at', 'created_by',
    ];

    protected $casts = [
        'amount'                  => 'decimal:2',
        'max_discount_amount'     => 'decimal:2',
        'min_order_amount'        => 'decimal:2',
        'is_active'               => 'boolean',
        'auto_apply'              => 'boolean',
        'starts_at'               => 'datetime',
        'expires_at'              => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function exclusiveCustomers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'coupon_customers');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_products');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    // ── State helpers ──────────────────────────────────────────────────────────

    public function isCurrentlyActive(): bool
    {
        if (! $this->is_active) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;

        return true;
    }

    public function isProductSpecific(): bool
    {
        return $this->relationLoaded('products')
            ? $this->products->isNotEmpty()
            : $this->products()->exists();
    }

    /**
     * Calculate discount against cart items, respecting product restrictions.
     * Each item needs 'product_id' and 'subtotal' keys.
     * Falls back to full order total when no product restriction is set.
     */
    public function calculateDiscountForItems(array $items): float
    {
        if (! $this->isProductSpecific()) {
            return $this->calculateDiscount((float) collect($items)->sum('subtotal'));
        }

        $restrictedIds = $this->products->pluck('id')->all();

        $applicableTotal = (float) collect($items)
            ->filter(fn ($item) => in_array($item['product_id'] ?? null, $restrictedIds, true))
            ->sum('subtotal');

        if ($applicableTotal <= 0) {
            return 0.0;
        }

        return $this->calculateDiscount($applicableTotal);
    }

    /**
     * Calculate the actual discount for a given order total.
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if ($this->type === 'fixed') {
            return min((float) $this->amount, $orderTotal);
        }

        $discount = $orderTotal * ($this->amount / 100);

        if ($this->max_discount_amount) {
            $discount = min($discount, (float) $this->max_discount_amount);
        }

        return round($discount, 2);
    }

    /**
     * Check if a customer is eligible for this coupon.
     */
    public function isEligibleFor(Customer $customer): bool
    {
        return match ($this->eligibility_rule) {
            'public'             => true,
            'first_order'        => $customer->orders()->doesntExist(),
            'return_customer'    => $customer->orders()->exists(),
            'long_time_purchaser' => $this->checkLongTimePurchaser($customer),
            'exclusive'          => $this->exclusiveCustomers()->where('customer_id', $customer->id)->exists(),
            default              => false,
        };
    }

    /**
     * Check if this coupon should be auto-applied for a customer at a given order total.
     */
    public function meetsAutoApplyCriteria(Customer $customer, float $orderTotal): bool
    {
        if (! $this->auto_apply || ! $this->isCurrentlyActive()) return false;
        if ($this->min_order_amount && $orderTotal < (float) $this->min_order_amount) return false;
        if ($this->auto_apply_min_orders > 0 && $customer->orders()->count() < $this->auto_apply_min_orders) return false;

        return $this->isEligibleFor($customer);
    }

    private function checkLongTimePurchaser(Customer $customer): bool
    {
        $months = $this->eligibility_months ?? 3;
        $firstOrder = $customer->orders()->oldest()->first();

        if (! $firstOrder) return false;

        return $firstOrder->created_at->diffInMonths(now()) >= $months;
    }
}
