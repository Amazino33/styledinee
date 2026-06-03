<?php

namespace App\Console\Commands;

use App\Models\OrderAssignment;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendProductionReminders extends Command
{
    protected $signature   = 'production:send-reminders';
    protected $description = 'Notify staff when 90% of estimated production time has elapsed on their active assignment.';

    public function handle(NotificationService $notif): void
    {
        $due = OrderAssignment::with([
                'assignedTo',
                'orderItem.product',
                'order',
            ])
            ->whereIn('status', ['assigned', 'in_progress'])
            ->whereNull('reminder_sent_at')
            ->whereHas('orderItem.product', fn ($q) =>
                $q->whereNotNull('estimated_production_hours')
                  ->where('estimated_production_hours', '>', 0)
            )
            ->get()
            ->filter(function (OrderAssignment $assignment): bool {
                $hours = $assignment->orderItem?->product?->estimated_production_hours;

                if (! $hours || ! $assignment->assigned_at) {
                    return false;
                }

                $ninetyPctMark = $assignment->assigned_at->addMinutes((int) ($hours * 60 * 0.9));

                return now()->gte($ninetyPctMark);
            });

        foreach ($due as $assignment) {
            try {
                $notif->productionReminderDue($assignment);

                $assignment->update(['reminder_sent_at' => now()]);

                $this->info("Reminded: {$assignment->assignedTo?->name} — assignment #{$assignment->id}");
            } catch (\Throwable $e) {
                $this->warn("Failed for assignment #{$assignment->id}: {$e->getMessage()}");
            }
        }

        $this->info("Done. {$due->count()} reminder(s) sent.");
    }
}
