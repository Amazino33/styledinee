<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Affiliate extends Model
{
    protected $fillable = [
        'username', 'name', 'email', 'phone',
        'customer_id', 'user_id',
        'commission_rate',
        'referral_payout_type', 'affiliate_payout_type',
        'bank_name', 'account_number', 'account_name',
        'status', 'approved_by', 'approved_at', 'notes',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'approved_at'     => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    public function referredCustomers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Effective commission rate — falls back to the global setting if not overridden.
     */
    public function effectiveCommissionRate(): float
    {
        return (float) ($this->commission_rate ?? AppSetting::get('affiliate_default_rate', 5));
    }

    /**
     * Current referral credit balance from the ledger.
     */
    public function creditBalance(): float
    {
        return (float) ReferralCreditLedger::balanceFor($this->username);
    }
}
