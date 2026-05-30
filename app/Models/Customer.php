<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'address',
        'username', 'password', 'referred_by_username', 'affiliate_id',
    ];

    protected $hidden = ['remember_token', 'password'];

    protected $casts = ['password' => 'hashed'];

    // ── Accessors / mutators ───────────────────────────────────────────────────

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => self::normalizePhone($value),
        );
    }

    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '234')) return '+' . $digits;
        if (str_starts_with($digits, '0'))   return '+234' . substr($digits, 1);

        return '+' . $digits;
    }

    public function needsUsername(): bool
    {
        return empty($this->username);
    }

    // ── Wallet helpers ─────────────────────────────────────────────────────────

    public function creditBalance(): float
    {
        if (! $this->username) return 0.0;

        return ReferralCreditLedger::balanceFor($this->username);
    }

    public function creditLedger(): HasMany
    {
        return $this->hasMany(ReferralCreditLedger::class, 'owner_username', 'username');
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(CustomerMeasurement::class);
    }

    public function otps(): HasMany
    {
        return $this->hasMany(CustomerOtp::class, 'phone', 'phone');
    }

    public function exclusiveCoupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_customers');
    }

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function measurementForProduct(int $productId): ?CustomerMeasurement
    {
        return $this->measurements()->where('product_id', $productId)->first();
    }
}
