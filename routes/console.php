<?php

use App\Jobs\StageProgressionJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-advance production item stages every 30 minutes
Schedule::job(StageProgressionJob::class)->everyThirtyMinutes();
