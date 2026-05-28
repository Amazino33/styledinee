<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralConversion extends Model
{
    protected $fillable = [
        'referrer_username', 'referrer_type', 'referrer_entity_id',
        'referred_customer_id', 'order_id',
        'reward_amount', 'payout_type', 'status',
        'processed_at', 'notes',
    ];

    protected $casts = [
        'reward_amount' => 'decimal:2',
        'processed_at'  => 'datetime',
    ];

    public function referredCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referred_customer_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
