<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StageProgressionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Production stages are now cashier-driven via the Production Tracker page.
     * This job is kept as a placeholder for any future scheduled automation.
     */
    public function handle(): void
    {
        // No-op: cashier manually advances item stages through the Production Tracker.
    }
}
