<?php

namespace App\Jobs;

use App\Models\BroadcastLog;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendBroadcastJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(
        public readonly int $broadcastLogId,
        public readonly string $phone,
        public readonly string $message,
    ) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        $sent = $whatsapp->send($this->phone, $this->message);

        $log = BroadcastLog::find($this->broadcastLogId);

        if ($sent) {
            $log?->increment('sent_count');
        } else {
            $log?->increment('failed_count');
        }
    }
}
