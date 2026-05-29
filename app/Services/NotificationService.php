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
        $message = "Hi {$order->customer_name}, your order {$order->reference} has been received. "
            . "Track it at: " . route('order.track', $order->reference);

        $this->whatsapp->send($order->customer_phone, $message);
        $this->sendEmail($order->customer_email, "Order Confirmed – {$order->reference}", $message);
    }

    public function stageUpdated(Order $order, string $stage, string $clientMessage): void
    {
        $message = "Hi {$order->customer_name}, update on {$order->reference}: {$clientMessage}";

        $this->whatsapp->send($order->customer_phone, $message);
        $this->sendEmail($order->customer_email, "Order Update – {$order->reference}", $message);
    }

    public function notifyOrderReady(Order $order): void
    {
        $message = "Hi {$order->customer_name}, great news! Your order {$order->reference} is ready for collection at Styledinee. "
            . "Please come in at your earliest convenience.";

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
        if (! $staff?->phone) return;

        $stageLabel = OrderItem::PRODUCTION_STAGES[$assignment->department]
            ?? ucfirst(str_replace('_', ' ', $assignment->department));

        $reference = $assignment->order->reference ?? 'an order';
        $itemDesc  = $assignment->orderItem->description ?? 'an item';

        $message = "Hi {$staff->name}, you have been assigned to a *{$stageLabel}* task "
            . "for order {$reference} ({$itemDesc}). Please check the production tracker.";

        if ($assignment->notes) {
            $message .= "\n\nNote: {$assignment->notes}";
        }

        $this->whatsapp->send($staff->phone, $message);
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
