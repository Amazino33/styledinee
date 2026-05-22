<?php

namespace App\Filament\Pages;

use App\Models\DeliveryOtp;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\User;
use App\Services\NotificationService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DeliveryQueue extends Page
{
    protected string $view = 'filament.pages.delivery-queue';
    protected static ?string $navigationLabel = 'Delivery Queue';
    protected static ?string $title = 'Delivery Queue';
    protected static ?int $navigationSort = 4;

    public static function getNavigationIcon(): string { return 'heroicon-o-truck'; }
    public static function getNavigationGroup(): ?string { return 'Operations'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'cashier', 'delivery']);
    }

    // ── OTP verify state ───────────────────────────────────
    public string $otpInput      = '';
    public ?int   $verifyOrderId = null;

    // ── Payment gate (shown after OTP passes but balance outstanding) ──
    public bool   $paymentWarning     = false;
    public bool   $otpVerified        = false;
    public float  $paymentBalanceDue  = 0.0;
    public string $paymentStatusLabel = '';

    // ── Cash collection sub-screen ─────────────────────────
    public bool   $showCashCollect  = false;
    public string $cashCollectInput = '';

    // ── Delivery assignment modal state ────────────────────
    public bool  $showDeliveryModal = false;
    public ?int  $deliveryOrderId   = null;
    public ?int  $deliveryUserId    = null;

    // ── Queries ────────────────────────────────────────────
    public function getOrders()
    {
        $query = Order::with(['items', 'deliveryUser', 'latestOtp'])
            ->whereIn('status', ['ready', 'dispatched'])
            ->latest();

        if (auth()->user()?->hasRole('delivery')) {
            $query->where('delivery_user_id', auth()->id());
        }

        return $query->get();
    }

    public function getDeliveryStaff(): array
    {
        return User::role('delivery')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    // ── Delivery assignment modal ──────────────────────────
    public function openDeliveryModal(int $orderId): void
    {
        $order = Order::find($orderId);
        if (! $order) return;

        $this->deliveryOrderId = $orderId;
        $this->deliveryUserId  = $order->delivery_user_id;
        $this->showDeliveryModal = true;
    }

    public function confirmDeliveryAssignment(): void
    {
        $order = Order::find($this->deliveryOrderId);
        if (! $order) {
            $this->showDeliveryModal = false;
            return;
        }

        $order->update(['delivery_user_id' => $this->deliveryUserId ?: null]);

        if ($this->deliveryUserId) {
            $person = User::find($this->deliveryUserId);

            Notification::make()
                ->title('New Delivery Assigned')
                ->body("Order {$order->reference} has been assigned to you for delivery.")
                ->sendToDatabase($person);

            Notification::make()
                ->title("Assigned to {$person?->name}")
                ->success()
                ->send();
        } else {
            Notification::make()->title('Delivery assignment cleared.')->success()->send();
        }

        $this->showDeliveryModal = false;
        $this->deliveryOrderId   = null;
        $this->deliveryUserId    = null;
    }

    public function cancelDeliveryModal(): void
    {
        $this->showDeliveryModal = false;
        $this->deliveryOrderId   = null;
        $this->deliveryUserId    = null;
    }

    // ── Dispatch ───────────────────────────────────────────
    public function dispatchOrder(int $orderId): void
    {
        $order = Order::find($orderId);
        if (! $order) return;

        // Delivery orders must have a delivery person assigned
        if ($order->delivery_type === 'delivery' && ! $order->delivery_user_id) {
            Notification::make()
                ->title('Assign a delivery person first')
                ->danger()
                ->send();
            return;
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        DeliveryOtp::create([
            'order_id'   => $orderId,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(30),
        ]);

        $order->update(['status' => 'dispatched']);
        $order->items()->update(['item_stage' => 'dispatched', 'stage_updated_at' => now()]);

        OrderStatusLog::create([
            'order_id'       => $orderId,
            'changed_by'     => auth()->id(),
            'status'         => 'dispatched',
            'notes'          => 'Dispatched for delivery.',
            'is_published'   => true,
            'client_message' => 'Your order is on the way! Use OTP ' . $otp . ' to confirm receipt.',
        ]);

        try {
            app(NotificationService::class)->sendDeliveryOtp($order, $otp);
        } catch (\Throwable) {}

        Notification::make()
            ->title("Order {$order->reference} dispatched")
            ->body("Customer OTP: {$otp}")
            ->persistent()
            ->success()
            ->send();
    }

    // ── OTP verification ───────────────────────────────────
    public function openVerify(int $orderId): void
    {
        $this->verifyOrderId      = $orderId;
        $this->otpInput           = '';
        $this->paymentWarning     = false;
        $this->otpVerified        = false;
        $this->paymentBalanceDue  = 0.0;
        $this->paymentStatusLabel = '';
        $this->showCashCollect    = false;
        $this->cashCollectInput   = '';
    }

    public function verifyOtp(): void
    {
        $order = Order::find($this->verifyOrderId);
        if (! $order) return;

        $latest = $order->latestOtp;
        if (! $latest || $latest->isExpired() || $latest->isVerified()) {
            Notification::make()->title('OTP invalid or expired.')->danger()->send();
            return;
        }

        if ($latest->otp !== $this->otpInput) {
            Notification::make()->title('Incorrect OTP. Try again.')->danger()->send();
            return;
        }

        // OTP is correct — check payment before completing delivery
        $this->otpVerified = true;

        if ($order->payment_status !== 'paid') {
            $this->paymentBalanceDue  = round((float) $order->total_amount - (float) $order->amount_paid, 2);
            $this->paymentStatusLabel = ucfirst($order->payment_status);
            $this->paymentWarning     = true;
            return;
        }

        $this->completeDelivery($order, $latest);
    }

    public function recheckPayment(): void
    {
        if (! $this->otpVerified) return;

        $order = Order::find($this->verifyOrderId);
        if (! $order) return;

        if ($order->payment_status !== 'paid') {
            $this->paymentBalanceDue  = round((float) $order->total_amount - (float) $order->amount_paid, 2);
            $this->paymentStatusLabel = ucfirst($order->payment_status);
            Notification::make()
                ->title('Payment still outstanding')
                ->body('₦' . number_format($this->paymentBalanceDue, 0) . ' remaining.')
                ->warning()
                ->send();
            return;
        }

        $latest = $order->latestOtp;
        $this->completeDelivery($order, $latest);
    }

    // ── Cash collection ────────────────────────────────────
    public function openCashCollect(): void
    {
        $this->cashCollectInput = number_format($this->paymentBalanceDue, 2, '.', '');
        $this->showCashCollect  = true;
    }

    public function cancelCashCollect(): void
    {
        $this->showCashCollect  = false;
        $this->cashCollectInput = '';
    }

    public function confirmCashCollect(): void
    {
        if (! $this->otpVerified) return;

        $collected = (float) str_replace(',', '', $this->cashCollectInput);

        if ($collected <= 0) {
            Notification::make()->title('Enter the amount collected.')->warning()->send();
            return;
        }

        $order = Order::find($this->verifyOrderId);
        if (! $order) return;

        $newAmountPaid = round((float) $order->amount_paid + $collected, 2);
        $total         = (float) $order->total_amount;

        if ($newAmountPaid > $total) {
            Notification::make()
                ->title('Amount exceeds balance')
                ->body('Maximum collectable is ₦' . number_format($this->paymentBalanceDue, 0) . '.')
                ->danger()
                ->send();
            return;
        }

        $newStatus = $newAmountPaid >= $total ? 'paid' : 'partial';

        $order->update([
            'amount_paid'    => $newAmountPaid,
            'payment_status' => $newStatus,
        ]);

        $order->recordPayment($collected, 'cash', 'Cash collected on delivery by ' . auth()->user()->name . '.');

        // Notify cashier/admin of the cash collected
        $driverName = auth()->user()->name;
        $admins = User::role(['admin', 'cashier'])->get();
        if ($admins->isNotEmpty()) {
            Notification::make()
                ->title('Cash collected on delivery')
                ->body("₦" . number_format($collected, 0) . " collected by {$driverName} for order {$order->reference}.")
                ->sendToDatabase($admins);
        }

        $this->showCashCollect  = false;
        $this->cashCollectInput = '';

        if ($newStatus !== 'paid') {
            // Partial — still outstanding, refresh the warning
            $this->paymentBalanceDue  = round($total - $newAmountPaid, 2);
            $this->paymentStatusLabel = 'Partial';
            Notification::make()
                ->title('Payment updated')
                ->body('₦' . number_format($this->paymentBalanceDue, 0) . ' still outstanding.')
                ->warning()
                ->send();
            return;
        }

        $latest = $order->latestOtp;
        $this->completeDelivery($order, $latest);
    }

    private function completeDelivery(Order $order, $otp): void
    {
        if ($otp) {
            $otp->update(['verified_at' => now()]);
        }

        $order->update(['status' => 'delivered']);
        $order->items()->update(['item_stage' => 'delivered', 'stage_updated_at' => now()]);

        OrderStatusLog::create([
            'order_id'       => $order->id,
            'changed_by'     => auth()->id(),
            'status'         => 'delivered',
            'notes'          => 'OTP verified. Delivery confirmed.',
            'is_published'   => true,
            'client_message' => 'Your order has been delivered successfully.',
        ]);

        $reference = $order->reference;

        $this->verifyOrderId      = null;
        $this->otpInput           = '';
        $this->paymentWarning     = false;
        $this->otpVerified        = false;
        $this->paymentBalanceDue  = 0.0;
        $this->paymentStatusLabel = '';
        $this->showCashCollect    = false;
        $this->cashCollectInput   = '';

        Notification::make()->title("Delivery confirmed for {$reference}.")->success()->send();
    }

    public function cancelVerify(): void
    {
        $this->verifyOrderId      = null;
        $this->otpInput           = '';
        $this->paymentWarning     = false;
        $this->otpVerified        = false;
        $this->paymentBalanceDue  = 0.0;
        $this->paymentStatusLabel = '';
        $this->showCashCollect    = false;
        $this->cashCollectInput   = '';
    }
}
