<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use App\Models\OrderAssignment;
use App\Models\OrderItem;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class TailorQueue extends Page
{
    protected string $view = 'filament.pages.tailor-queue';
    protected static ?string $navigationLabel = 'Tailor Queue';
    protected static ?string $title = 'Tailor Queue';
    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): string { return 'heroicon-o-scissors'; }
    public static function getNavigationGroup(): ?string { return 'Operations'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'tailor']);
    }

    // ── Details modal state ─────────────────────────────────
    public bool $showDetailsModal    = false;
    public ?int $detailsAssignmentId = null;

    public function openDetailsModal(int $assignmentId): void
    {
        $this->detailsAssignmentId = $assignmentId;
        $this->showDetailsModal    = true;
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal    = false;
        $this->detailsAssignmentId = null;
    }

    // ── Admin header action: toggle customer contact visibility ──
    protected function getHeaderActions(): array
    {
        if (! auth()->user()?->hasRole('admin')) return [];

        $showContact = AppSetting::bool('tailor_queue_show_customer_contact', false);

        return [
            Action::make('toggleContact')
                ->label($showContact ? 'Hide Customer Contact Info' : 'Show Customer Contact Info')
                ->icon($showContact ? 'heroicon-o-eye-slash' : 'heroicon-o-phone')
                ->color($showContact ? 'gray' : 'success')
                ->action(function () use ($showContact) {
                    AppSetting::set('tailor_queue_show_customer_contact', $showContact ? '0' : '1');
                    $label = $showContact ? 'Contact info hidden from tailors.' : 'Contact info now visible to tailors.';
                    Notification::make()->title($label)->success()->send();
                }),
        ];
    }

    public function getMyAssignments()
    {
        return OrderAssignment::with([
            'order.customer',
            'orderItem.variant',
            'orderItem.product.measurementTemplate',
        ])
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

        if ($this->showDetailsModal) {
            $this->closeDetailsModal();
        }

        Notification::make()->title("Done — advanced to {$nextLabel}.")->success()->send();
    }
}
