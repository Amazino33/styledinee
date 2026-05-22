<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'reference', 'user_id', 'customer_id', 'service_id',
        'customer_name', 'customer_email', 'customer_phone', 'customer_address',
        'type', 'status', 'notes', 'total_amount', 'amount_paid', 'payment_status',
        'pickup_date', 'delivery_date', 'estimated_completion_date',
        'delivery_type', 'delivery_notes', 'delivery_user_id',
    ];

    protected $casts = [
        'total_amount'              => 'decimal:2',
        'amount_paid'               => 'decimal:2',
        'pickup_date'               => 'date',
        'delivery_date'             => 'date',
        'estimated_completion_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            if (empty($order->reference)) {
                $order->reference = 'STD-' . strtoupper(Str::random(8));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryUser()
    {
        return $this->belongsTo(User::class, 'delivery_user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    public function assignments()
    {
        return $this->hasMany(OrderAssignment::class);
    }

    public function deliveryOtps()
    {
        return $this->hasMany(DeliveryOtp::class);
    }

    public function latestOtp()
    {
        return $this->hasOne(DeliveryOtp::class)->latestOfMany();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function recordPayment(float $amount, string $method = 'cash', ?string $notes = null): Payment
    {
        return Payment::record($this, $amount, $method, $notes);
    }

    /**
     * Update the order status to reflect the current bottleneck stage
     * across all production items. Called whenever any item stage changes.
     */
    /**
     * Called whenever any item stage changes.
     * Keeps the order status simple for the cashier:
     * any active production work = in_progress, all done = ready.
     */
    public function syncStatusFromItems(): void
    {
        $productionItems = $this->items()->where('production_type', 'production')->get();

        if ($productionItems->isEmpty()) return;

        $allDone = $productionItems->every(
            fn ($i) => in_array($i->item_stage, ['ready', 'dispatched', 'delivered'])
        );

        $newStatus = $allDone ? 'ready' : 'in_progress';

        if ($this->status === $newStatus) return;

        $this->update(['status' => $newStatus]);

        if ($newStatus === 'ready') {
            try {
                app(\App\Services\NotificationService::class)->notifyOrderReady($this);
            } catch (\Throwable) {}
        }
    }
}
