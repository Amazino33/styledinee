<?php

namespace App\Filament\Pages;

use App\Models\OrderAssignment;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ProductionTracker extends Page
{
    protected string $view = 'filament.pages.production-tracker';
    protected static ?string $navigationLabel = 'Production';
    protected static ?string $title = 'Production Tracker';
    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): string { return 'heroicon-o-clipboard-document-list'; }
    public static function getNavigationGroup(): ?string { return 'Operations'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'cashier']);
    }

    public const STAGE_ROLES = [
        'sewing'     => 'tailor',
        'embroidery' => 'embroidery',
        'printing'   => 'printer',
        'finishing'  => 'dry_cleaner',
    ];

    public const STAGE_DEFINITIONS = [
        'sewing'     => ['label' => 'Sewing',     'role_label' => 'Tailors'],
        'embroidery' => ['label' => 'Embroidery', 'role_label' => 'Embroiderers'],
        'printing'   => ['label' => 'Printing',   'role_label' => 'Printers'],
        'finishing'  => ['label' => 'Finishing',  'role_label' => 'Dry Cleaners'],
    ];

    // ── Assignment modal state ──────────────────────────────
    public bool   $showAssignModal = false;
    public ?int   $assignItemId    = null;
    public string $assignStage     = '';
    public ?int   $assignStaffId   = null;
    public string $assignNotes     = '';

    // ── Details modal state ─────────────────────────────────
    public bool $showDetailsModal = false;
    public ?int $detailsItemId    = null;

    public function getItemsByStage(): array
    {
        $stages = array_keys(self::STAGE_ROLES);

        $items = OrderItem::with(['order', 'activeAssignment.assignedTo', 'variant'])
            ->where('production_type', 'production')
            ->whereNotNull('production_path')
            ->whereIn('item_stage', $stages)
            ->whereHas('order', fn ($q) => $q->whereIn('status', ['confirmed', 'in_progress']))
            ->orderBy('stage_updated_at')
            ->get();

        $grouped = [];
        foreach ($stages as $stage) {
            $grouped[$stage] = $items->where('item_stage', $stage)->values();
        }

        return $grouped;
    }

    public function hasStageInTracker(string $stage): bool
    {
        return OrderItem::whereJsonContains('production_path', $stage)
            ->whereIn('item_stage', [$stage, prev(array_keys(self::STAGE_ROLES))])
            ->whereHas('order', fn ($q) => $q->whereIn('status', ['confirmed', 'in_progress']))
            ->exists();
    }

    public function hasEmbroideryInTracker(): bool
    {
        return OrderItem::whereJsonContains('production_path', 'embroidery')
            ->whereIn('item_stage', ['sewing', 'embroidery'])
            ->whereHas('order', fn ($q) => $q->whereIn('status', ['confirmed', 'in_progress']))
            ->exists();
    }

    public function hasPrintingInTracker(): bool
    {
        return OrderItem::whereJsonContains('production_path', 'printing')
            ->whereIn('item_stage', ['sewing', 'embroidery', 'printing'])
            ->whereHas('order', fn ($q) => $q->whereIn('status', ['confirmed', 'in_progress']))
            ->exists();
    }

    public function getStaffForStage(string $stage): array
    {
        $role = self::STAGE_ROLES[$stage] ?? null;
        if (! $role) return [];

        return User::role($role)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    // ── Details modal ───────────────────────────────────────
    public function openDetailsModal(int $itemId): void
    {
        $this->detailsItemId    = $itemId;
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->detailsItemId    = null;
    }

    // ── Advance stage ───────────────────────────────────────
    public function advanceStage(int $itemId): void
    {
        $item = OrderItem::with(['order', 'activeAssignment'])->find($itemId);
        if (! $item) return;

        if (! $item->activeAssignment) {
            Notification::make()
                ->title('No staff assigned')
                ->body('Assign a staff member before advancing this item.')
                ->warning()
                ->send();
            return;
        }

        if (! $item->staff_marked_done) {
            Notification::make()
                ->title('Cannot Advance')
                ->body('Staff has not marked this item as done yet.')
                ->danger()
                ->send();
            return;
        }

        $next = $item->nextStage();
        if (! $next) return;

        $item->advanceToNextStage(auth()->id());

        $nextLabel = OrderItem::PRODUCTION_STAGES[$next] ?? ucfirst($next);
        Notification::make()->title("{$item->description} → {$nextLabel}")->success()->send();
    }

    // ── Assignment modal ────────────────────────────────────
    public function openAssignModal(int $itemId, string $stage): void
    {
        if (! array_key_exists($stage, OrderItem::PRODUCTION_STAGES)) return;

        $this->assignItemId  = $itemId;
        $this->assignStage   = $stage;
        $this->assignStaffId = null;
        $this->assignNotes   = '';

        $existing = OrderAssignment::where('order_item_id', $itemId)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->latest()
            ->first();
        if ($existing) {
            $this->assignStaffId = $existing->assigned_to;
        }

        $this->showAssignModal = true;
    }

    public function confirmAssign(): void
    {
        $item = OrderItem::find($this->assignItemId);
        if (! $item) {
            $this->showAssignModal = false;
            return;
        }

        $item->update([
            'item_stage'        => $this->assignStage,
            'stage_updated_at'  => now(),
            'staff_marked_done' => false,
            'staff_done_at'     => null,
            'staff_done_by'     => null,
        ]);

        $order = $item->order;
        if ($order) {
            $order->syncStatusFromItems();
        }

        if ($this->assignStaffId) {
            OrderAssignment::where('order_item_id', $item->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->update(['status' => 'complete', 'completed_at' => now()]);

            $staffMember = User::find($this->assignStaffId);
            $department  = $staffMember?->getRoleNames()->first() ?? 'tailor';

            OrderAssignment::create([
                'order_id'      => $item->order_id,
                'order_item_id' => $item->id,
                'assigned_to'   => $this->assignStaffId,
                'assigned_by'   => auth()->id(),
                'department'    => $department,
                'status'        => 'assigned',
                'assigned_at'   => now(),
                'notes'         => $this->assignNotes ?: null,
            ]);

            $staffName = $staffMember?->name;
        }

        if ($order) {
            OrderStatusLog::create([
                'order_id'      => $order->id,
                'order_item_id' => $item->id,
                'changed_by'    => auth()->id(),
                'status'        => $this->assignStage,
                'notes'         => isset($staffName)
                    ? 'Stage set to ' . OrderItem::PRODUCTION_STAGES[$this->assignStage] . ", assigned to {$staffName}."
                    : 'Stage set to ' . OrderItem::PRODUCTION_STAGES[$this->assignStage] . '.',
                'is_published'  => false,
            ]);
        }

        $stageLabel = OrderItem::PRODUCTION_STAGES[$this->assignStage];
        $msg = isset($staffName)
            ? "{$item->description} → {$stageLabel} (assigned to {$staffName})"
            : "{$item->description} → {$stageLabel}";

        Notification::make()->title($msg)->success()->send();

        $this->showAssignModal = false;
        $this->assignItemId    = null;
        $this->assignStage     = '';
        $this->assignStaffId   = null;
        $this->assignNotes     = '';
    }

    public function cancelAssign(): void
    {
        $this->showAssignModal = false;
        $this->assignItemId    = null;
        $this->assignStage     = '';
    }
}
