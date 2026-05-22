<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    protected $fillable = [
        'order_id', 'order_item_id', 'changed_by', 'status', 'notes',
        'scheduled_at', 'is_published', 'client_message',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
