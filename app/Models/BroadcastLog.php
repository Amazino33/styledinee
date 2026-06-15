<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BroadcastLog extends Model
{
    protected $fillable = [
        'created_by', 'audience_type', 'message', 'recipient_count', 'sent_count', 'failed_count',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
