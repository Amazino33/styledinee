<?php

namespace App\Filament\Pages;

use App\Models\DailyReconciliation;
use App\Models\Order;
use App\Models\Payment;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class EndOfDay extends Page
{
    protected string $view = 'filament.pages.end-of-day';
    protected static ?string $navigationLabel = 'End of Day';
    protected static ?string $title           = 'End of Day Accounting';
    protected static ?int    $navigationSort  = 10;

    public static function getNavigationIcon(): string { return 'heroicon-o-calculator'; }
    public static function getNavigationGroup(): ?string { return 'Orders'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:EndOfDay') ?? false;
    }

    // ── State ────────────────────────────────────────────────────────────────
    public string $selectedDate = '';
    public string $cashCounted  = '';
    public string $eodNotes     = '';

    // ── Lifecycle ────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->selectedDate = now()->toDateString();
        $this->loadExisting();
    }

    public function updatedSelectedDate(): void
    {
        $this->cashCounted = '';
        $this->eodNotes    = '';
        $this->loadExisting();
    }

    private function loadExisting(): void
    {
        $rec = DailyReconciliation::where('date', $this->selectedDate)
            ->where('closed_by', auth()->id())
            ->first();

        if ($rec) {
            $this->cashCounted = (string) (float) $rec->total_cash_counted;
            $this->eodNotes    = $rec->notes ?? '';
        }
    }

    // ── Queries ──────────────────────────────────────────────────────────────
    public function getPaymentSummary(): array
    {
        $payments = Payment::whereDate('created_at', $this->selectedDate)->get();

        return [
            'cash'      => (float) $payments->where('method', 'cash')->sum('amount'),
            'transfer'  => (float) $payments->where('method', 'transfer')->sum('amount'),
            'card'      => (float) $payments->where('method', 'card')->sum('amount'),
            'pos'       => (float) $payments->where('method', 'pos')->sum('amount'),
            'total'     => (float) $payments->sum('amount'),
            'count'     => $payments->count(),
        ];
    }

    public function getOutstandingOrders(): \Illuminate\Support\Collection
    {
        return Order::whereDate('created_at', $this->selectedDate)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->whereNotIn('status', ['cancelled'])
            ->orderByDesc('created_at')
            ->get(['id', 'reference', 'customer_name', 'total_amount', 'amount_paid', 'payment_status']);
    }

    public function getPendingDriverCashCount(): int
    {
        return Order::where('driver_cash_pending', true)->count();
    }

    public function getExistingReconciliation(): ?DailyReconciliation
    {
        return DailyReconciliation::where('date', $this->selectedDate)
            ->where('closed_by', auth()->id())
            ->with('closedBy')
            ->first();
    }

    public function getRecentReconciliations(): \Illuminate\Support\Collection
    {
        $query = DailyReconciliation::with('closedBy')
            ->orderByDesc('date')
            ->limit(30);

        if (! auth()->user()?->hasRole('admin')) {
            $query->where('closed_by', auth()->id());
        }

        return $query->get();
    }

    // ── Actions ──────────────────────────────────────────────────────────────
    public function closeDay(): void
    {
        $this->validate([
            'cashCounted' => 'required|numeric|min:0',
        ], [
            'cashCounted.required' => 'Enter the physical cash count.',
            'cashCounted.numeric'  => 'Cash count must be a number.',
            'cashCounted.min'      => 'Cash count cannot be negative.',
        ]);

        $summary     = $this->getPaymentSummary();
        $outstanding = $this->getOutstandingOrders();
        $driverPending = $this->getPendingDriverCashCount();

        $cashExpected = $summary['cash'];
        $cashCounted  = (float) $this->cashCounted;
        $discrepancy  = round($cashCounted - $cashExpected, 2);

        DailyReconciliation::updateOrCreate(
            ['date' => $this->selectedDate, 'closed_by' => auth()->id()],
            [
                'total_cash_expected'       => $cashExpected,
                'total_cash_counted'        => $cashCounted,
                'total_transfers'           => $summary['transfer'],
                'total_card'                => $summary['card'],
                'total_pos'                 => $summary['pos'],
                'total_all'                 => $summary['total'],
                'discrepancy'               => $discrepancy,
                'outstanding_orders_count'  => $outstanding->count(),
                'pending_driver_cash_count' => $driverPending,
                'notes'                     => trim($this->eodNotes) ?: null,
            ]
        );

        $label = match(true) {
            $discrepancy === 0.0  => 'Cash balanced.',
            $discrepancy > 0      => 'Cash overage of ₦' . number_format($discrepancy, 2) . '.',
            default               => 'Cash shortage of ₦' . number_format(abs($discrepancy), 2) . '.',
        };

        Notification::make()
            ->title('Day closed — ' . $label)
            ->body('Reconciliation saved for ' . \Carbon\Carbon::parse($this->selectedDate)->format('d M Y') . '.')
            ->success()
            ->send();
    }
}
