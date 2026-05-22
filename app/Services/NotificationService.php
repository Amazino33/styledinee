<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    // WAPM API base URL — set WAPM_API_KEY and WAPM_FROM in .env when available
    private string $wapmKey;
    private string $wapmFrom;

    public function __construct()
    {
        $this->wapmKey  = config('services.wapm.key', '');
        $this->wapmFrom = config('services.wapm.from', '');
    }

    /**
     * Send order confirmation to customer.
     */
    public function orderConfirmed(Order $order): void
    {
        $message = "Hi {$order->customer_name}, your order {$order->reference} has been received. "
            . "Track it at: " . route('order.track', $order->reference);

        $this->sendWhatsApp($order->customer_phone, $message);
        $this->sendEmail($order->customer_email, "Order Confirmed – {$order->reference}", $message);
    }

    /**
     * Send a stage update notification.
     */
    public function stageUpdated(Order $order, string $stage, string $clientMessage): void
    {
        $message = "Hi {$order->customer_name}, update on {$order->reference}: {$clientMessage}";

        $this->sendWhatsApp($order->customer_phone, $message);
        $this->sendEmail($order->customer_email, "Order Update – {$order->reference}", $message);
    }

    /**
     * Notify customer their order is ready for pickup.
     */
    public function notifyOrderReady(Order $order): void
    {
        $message = "Hi {$order->customer_name}, great news! Your order {$order->reference} is ready for collection at Styledinee. "
            . "Please come in at your earliest convenience.";

        $this->sendWhatsApp($order->customer_phone, $message);
        $this->sendEmail(
            $order->customer_email,
            "Your Order is Ready – {$order->reference}",
            $message
        );
    }

    /**
     * Send OTP for delivery verification.
     */
    public function sendDeliveryOtp(Order $order, string $otp): void
    {
        $message = "Your Styledinee delivery OTP for order {$order->reference} is: {$otp}. "
            . "Valid for 30 minutes. Share only with your delivery agent.";

        $this->sendWhatsApp($order->customer_phone, $message);
        $this->sendEmail($order->customer_email, "Delivery OTP – {$order->reference}", $message);
    }

    // ── Private Senders ───────────────────────────────────

    private function sendWhatsApp(string $phone, string $message): void
    {
        if (empty($this->wapmKey)) {
            Log::info("[WAPM stub] Would send WhatsApp to {$phone}: {$message}");
            return;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($this->wapmKey)
                ->post('https://api.wapm.io/v1/messages/send', [
                    'from'    => $this->wapmFrom,
                    'to'      => $phone,
                    'message' => $message,
                ]);

            if (! $response->successful()) {
                Log::error("[WAPM] Failed to send to {$phone}: " . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error("[WAPM] Exception: " . $e->getMessage());
        }
    }

    private function sendEmail(?string $email, string $subject, string $body): void
    {
        if (empty($email)) return;

        try {
            Mail::raw($body, function ($mail) use ($email, $subject) {
                $mail->to($email)->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::error("[Mail] Failed to {$email}: " . $e->getMessage());
        }
    }
}
