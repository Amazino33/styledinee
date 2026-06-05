<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReconciliation extends Model
{
    protected $fillable = [
        'date',
        'closed_by',
        'total_cash_expected',
        'total_cash_counted',
        'total_transfers',
        'total_card',
        'total_pos',
        'total_all',
        'discrepancy',
        'outstanding_orders_count',
        'pending_driver_cash_count',
        'notes',
    ];

    protected $casts = [
        'date'                      => 'date',
        'total_cash_expected'       => 'decimal:2',
        'total_cash_counted'        => 'decimal:2',
        'total_transfers'           => 'decimal:2',
        'total_card'                => 'decimal:2',
        'total_pos'                 => 'decimal:2',
        'total_all'                 => 'decimal:2',
        'discrepancy'               => 'decimal:2',
        'outstanding_orders_count'  => 'integer',
        'pending_driver_cash_count' => 'integer',
    ];

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function getDiscrepancyStatusAttribute(): string
    {
        $d = (float) $this->discrepancy;
        if ($d === 0.0) return 'balanced';
        return $d > 0 ? 'overage' : 'shortage';
    }
}
