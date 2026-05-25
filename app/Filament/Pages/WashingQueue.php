<?php

namespace App\Filament\Pages;

use App\Models\OrderAssignment;
use App\Models\OrderItem;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class WashingQueue extends Page
{
    protected string $view = 'filament.pages.washing-queue';
    protected static ?string $navigationLabel = 'Washing Queue';
    protected static ?string $title = 'Washing Queue';
    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'dry_cleaner']);
    }

    public function getMyAssignments()
    {
        return OrderAssignment::with(['order', 'orderItem'])
            ->where('assigned_to', auth()->id())
            ->where('status', '!=', 'complete')
            ->latest('assigned_at')
            ->get();
    }

    public function markDone(int $assignmentId): void
    {
        $assignment = OrderAssignment::find($assignmentId);
        if (! $assignment || $assignment->assigned_to !== auth()->id()) return;

        $item = $assignment->orderItem;
        if (! $item) return;

        $item->update([
            'staff_marked_done' => true,
            'staff_done_at'     => now(),
            'staff_done_by'     => auth()->id(),
        ]);

        $next = $item->nextStage();
        $item->advanceToNextStage(auth()->id());

        $order     = $assignment->order;
        $staffName = auth()->user()->name;
        $nextLabel = $next ? (OrderItem::PRODUCTION_STAGES[$next] ?? ucfirst($next)) : 'Ready';

        $cashiers = User::role('cashier')->get();
        if ($cashiers->isNotEmpty()) {
            Notification::make()
                ->title('Stage Advanced')
                ->body("Order {$order?->reference} — {$item->description} → {$nextLabel} (by {$staffName}).")
                ->sendToDatabase($cashiers);
        }

        Notification::make()->title("Done — advanced to {$nextLabel}.")->success()->send();
    }
}
