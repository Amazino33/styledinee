<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerBodyMeasurement extends Model
{
    protected $fillable = [
        'customer_id', 'measurements', 'unit', 'notes', 'is_active', 'taken_at',
    ];

    protected $casts = [
        'measurements' => 'array',
        'is_active'    => 'boolean',
        'taken_at'     => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** Activate this profile and deactivate all others for this customer. */
    public function setAsActive(): void
    {
        static::where('customer_id', $this->customer_id)
            ->where('id', '!=', $this->id)
            ->update(['is_active' => false]);

        $this->update(['is_active' => true]);
    }
}
