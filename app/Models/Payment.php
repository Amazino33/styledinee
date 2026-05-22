<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['order_id', 'amount', 'method', 'notes', 'recorded_by'];

    protected $casts = ['amount' => 'decimal:2'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function record(
        Order $order,
        float $amount,
        string $method = 'cash',
        ?string $notes = null
    ): self {
        return static::create([
            'order_id'    => $order->id,
            'amount'      => $amount,
            'method'      => $method,
            'notes'       => $notes,
            'recorded_by' => auth()->id(),
        ]);
    }

    public static function methodLabel(string $method): string
    {
        return match($method) {
            'cash'       => 'Cash',
            'transfer'   => 'Bank Transfer',
            'card'       => 'Card',
            'pos'        => 'POS Terminal',
            'adjustment' => 'Adjustment',
            default      => ucfirst($method),
        };
    }
}
