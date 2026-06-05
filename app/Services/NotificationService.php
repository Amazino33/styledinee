<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function __construct(private WhatsAppService $whatsapp) {}

    // ── Customer notifications ─────────────────────────────────────────────────

    public function orderConfirmed(Order $order): void
    {
        $trackUrl = route('account.order.track', $order->reference);
        $message  = "Hi {$order->customer_name}, your order *{$order->reference}* has been received and confirmed. "
            . "Track your order in real-time: {$trackUrl}";

        $this->whatsapp->send($order->customer_phone, $message);
        $this->sendEmail($order->customer_email, "Order Confirmed – {$order->reference}", $message);
    }

    public function stageUpdated(Order $order, string $clientMessage): void
    {
        $trackUrl = route('account.order.track', $order->reference);
        $message  = "Hi {$order->customer_name}, update on order *{$order->reference}*:\n\n"
            . "{$clientMessage}\n\n"
            . "Track your order: {$trackUrl}";

        $this->whatsapp->send($order->customer_phone, $message);
        $this->sendEmail($order->customer_email, "Order Update – {$order->reference}", $message);
    }

    public function notifyOrderReady(Order $order): void
    {
        $trackUrl = route('account.order.track', $order->reference);
        $message  = "Hi {$order->customer_name}, great news! 🎉 Your order *{$order->reference}* is ready for collection at Styledinee. "
            . "Please come in at your earliest convenience.\n\n"
            . "View order details: {$trackUrl}";

        $this->whatsapp->send($order->customer_phone, $message);
        $this->sendEmail($order->customer_email, "Your Order is Ready – {$order->reference}", $message);
    }

    public function orderHandedOver(Order $order): void
    {
        $deliveryName = $order->deliveryUser?->name ?? 'our delivery team';

        $message = "Hi {$order->customer_name}, your order *{$order->reference}* has been packed and handed to {$deliveryName} for delivery. "
            . "You will receive a delivery OTP once it is collected. Please keep your phone nearby.";

        $this->whatsapp->send($order->customer_phone, $message);
    }

    public function orderCollectedByRider(Order $order, string $otp): void
    {
        $message = "Hi {$order->customer_name}, your order *{$order->reference}* is on its way! "
            . "Show this OTP to our delivery person when they arrive: *{$otp}*\n\nValid for 4 hours. Do not share with anyone else.";

        $this->whatsapp->send($order->customer_phone, $message);
    }

    public function sendDeliveryOtp(Order $order, string $otp): void
    {
        $message = "Your Styledinee delivery OTP for order {$order->reference} is: *{$otp}*. "
            . "Valid for 30 minutes. Share only with your delivery agent.";

        $this->whatsapp->send($order->customer_phone, $message);
        $this->sendEmail($order->customer_email, "Delivery OTP – {$order->reference}", $message);
    }

    // ── Staff notifications ────────────────────────────────────────────────────

    /**
     * Notify a staff member that they have been assigned to a production task.
     */
    public function staffAssigned(OrderAssignment $assignment): void
    {
        $assignment->loadMissing(['assignedTo', 'order', 'orderItem']);

        $staff = $assignment->assignedTo;
        if (! $staff?->phone) {
            Log::warning("[Notify] staffAssigned: user #{$staff?->id} ({$staff?->name}) has no phone number — skipping WhatsApp.");
            return;
        }

        $stageLabel = OrderItem::PRODUCTION_STAGES[$assignment->department]
            ?? ucfirst(str_replace('_', ' ', $assignment->department));

        $reference = $assignment->order->reference ?? 'an order';
        $itemDesc  = $assignment->orderItem->description ?? 'an item';

        $message = "Hi {$staff->name}, you have been assigned to a *{$stageLabel}* task "
            . "for order {$reference} ({$itemDesc}). Please check your queue.";

        if ($assignment->notes) {
            $message .= "\n\nNote: {$assignment->notes}";
        }

        $this->whatsapp->send($staff->phone, $message);
    }

    /**
     * Remind a staff member that 90% of the estimated production time has elapsed.
     */
    public function productionReminderDue(\App\Models\OrderAssignment $assignment): void
    {
        $assignment->loadMissing(['assignedTo', 'orderItem.product', 'order.user']);

        $product    = $assignment->orderItem?->product;
        $hours      = $product?->estimated_production_hours ?? 0;
        $itemDesc   = $assignment->orderItem?->description ?? 'an item';
        $reference  = $assignment->order?->reference ?? 'an order';
        $stageLabel = \App\Models\OrderItem::PRODUCTION_STAGES[$assignment->department]
            ?? ucfirst(str_replace('_', ' ', $assignment->department ?? ''));

        // 1. Notify the assigned staff member
        $staff = $assignment->assignedTo;
        if ($staff?->phone) {
            $staffMessage = "⏰ Hi {$staff->name}, reminder: you have been working on "
                . "*{$itemDesc}* (order {$reference}) in the *{$stageLabel}* stage "
                . "for 90% of the estimated {$hours}h. "
                . "Please wrap up or flag if you need more time.";

            $this->whatsapp->send($staff->phone, $staffMessage);
        }

        // 2. Notify the cashier who created the order
        $cashier = $assignment->order?->user;
        if ($cashier && $cashier->phone && $cashier->id !== $staff?->id) {
            $cashierMessage = "⏰ Hi {$cashier->name}, heads up: *{$itemDesc}* "
                . "(order {$reference}, {$stageLabel} stage) assigned to "
                . ($staff?->name ?? 'a staff member')
                . " is at 90% of the estimated {$hours}h. Please follow up if needed.";

            $this->whatsapp->send($cashier->phone, $cashierMessage);
        }

        // 3. Notify all admins
        $adminMessage = "⏰ Production alert: *{$itemDesc}* (order {$reference}, "
            . "{$stageLabel} stage) assigned to "
            . ($staff?->name ?? 'a staff member')
            . " is at 90% of the estimated {$hours}h deadline.";

        User::role('admin')
            ->whereNotNull('phone')
            ->get()
            ->each(function (User $admin) use ($adminMessage, $cashier, $staff) {
                // Skip if admin is also the cashier or staff (already notified)
                if ($admin->id === $cashier?->id || $admin->id === $staff?->id) return;
                $this->whatsapp->send($admin->phone, "⏰ Hi {$admin->name}, {$adminMessage}");
            });
    }

    /**
     * Send a login OTP to a customer via WhatsApp.
     */
    public function sendAuthOtp(string $phone, string $otp): void
    {
        $message = "Your Styledinee login code is: *{$otp}*\n\nValid for 10 minutes. Do not share this code with anyone.";

        $this->whatsapp->send($phone, $message);
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function sendEmail(?string $email, string $subject, string $body): void
    {
        if (empty($email)) return;

        try {
            Mail::raw($body, function ($mail) use ($email, $subject) {
                $mail->to($email)->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::error("[Mail] Failed to send to {$email}: " . $e->getMessage());
        }
    }
}
