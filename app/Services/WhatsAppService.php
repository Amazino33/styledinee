<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\CustomerOtp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message via WAWP, with SMS fallback if configured.
     * Rate-limits OTP sends per phone number using the customer_otps window columns.
     */
    public function send(string $phone, string $message): bool
    {
        $sent = $this->sendWhatsApp($phone, $message);

        if (! $sent) {
            $sent = $this->sendSms($phone, $message);
        }

        return $sent;
    }

    /**
     * Check OTP rate limit for a phone number.
     * Returns true if allowed, false if the limit has been hit.
     * Window and max attempts are configurable via AppSetting.
     */
    public function checkOtpRateLimit(string $phone): bool
    {
        $windowMinutes = (int) AppSetting::get('otp_window_minutes', 10);
        $maxAttempts   = (int) AppSetting::get('otp_max_attempts', 3);

        $windowStart = now()->subMinutes($windowMinutes);

        $count = CustomerOtp::where('phone', $phone)
            ->where('created_at', '>=', $windowStart)
            ->count();

        return $count < $maxAttempts;
    }

    // ── Private ────────────────────────────────────────────────────────────────

    private function sendWhatsApp(string $phone, string $message): bool
    {
        $enabled    = AppSetting::bool('wawp_enabled', false);
        $instanceId = AppSetting::get('wawp_instance_id', '');
        $token      = AppSetting::get('wawp_access_token', '');

        if (! $enabled || empty($instanceId) || empty($token)) {
            Log::info("[WAWP stub] Would send to {$phone}: {$message}");
            return false;
        }

        $number = preg_replace('/\D/', '', $phone);

        // Normalise Nigerian numbers: 08012345678 → 2348012345678
        if (strlen($number) === 11 && str_starts_with($number, '0')) {
            $number = '234' . substr($number, 1);
        }

        try {
            $response = Http::get('https://api.wawp.net/v2/send/text', [
                'instance_id'  => $instanceId,
                'access_token' => $token,
                'chatId'       => $number . '@c.us',
                'message'      => $message,
            ]);

            // HTTP-level failure
            if (! $response->successful()) {
                Log::warning("[WAWP] HTTP error sending to {$phone} — falling back to SMS: " . $response->body());
                return false;
            }

            // Payload-level failure: WAWP may return 200 with an error body
            // (e.g. number not on WhatsApp, session disconnected, etc.)
            $json = $response->json();
            if (
                isset($json['error']) ||
                (isset($json['status']) && $json['status'] === false) ||
                (isset($json['success']) && $json['success'] === false)
            ) {
                Log::warning("[WAWP] Delivery failed for {$phone} — falling back to SMS: " . $response->body());
                return false;
            }

            Log::info("[WAWP] Sent to {$phone}");
            return true;
        } catch (\Throwable $e) {
            Log::error("[WAWP] Exception sending to {$phone} — falling back to SMS: " . $e->getMessage());
            return false;
        }
    }

    private function sendSms(string $phone, string $message): bool
    {
        $enabled  = AppSetting::bool('sms_enabled', false);
        $provider = AppSetting::get('sms_provider', '');   // 'termii' | 'bulksms' | 'kudisms'
        $apiKey   = AppSetting::get('sms_api_key', '');
        $senderId = AppSetting::get('sms_sender_id', 'Styledinee');

        if (! $enabled || empty($apiKey) || empty($provider)) {
            Log::info("[SMS stub] Would send to {$phone}: {$message}");
            return false;
        }

        $number = preg_replace('/\D/', '', $phone);

        try {
            if ($provider === 'termii') {
                $response = Http::post('https://api.ng.termii.com/api/sms/send', [
                    'to'      => $number,
                    'from'    => $senderId,
                    'sms'     => $message,
                    'type'    => 'plain',
                    'channel' => 'generic',
                    'api_key' => $apiKey,
                ]);

                if (! $response->successful()) {
                    Log::error("[SMS] Termii failed to send to {$phone}: " . $response->body());
                    return false;
                }

            } elseif ($provider === 'bulksms') {
                $response = Http::withBasicAuth($apiKey, AppSetting::get('sms_api_secret', ''))
                    ->post('https://api.bulksms.com/v1/messages', [
                        ['to' => $number, 'body' => $message],
                    ]);

                if (! $response->successful()) {
                    Log::error("[SMS] BulkSMS failed to send to {$phone}: " . $response->body());
                    return false;
                }

            } elseif ($provider === 'kudisms') {
                $response = Http::withToken($apiKey)
                    ->post('https://my.kudisms.net/api/autocomposesms', [
                        'token'   => $apiKey,
                        'gateway' => 2,
                        'data'    => [[$senderId, $number, $message]],
                    ]);

                if (! $response->successful()) {
                    Log::error("[SMS] KudiSMS failed to send to {$phone}: " . $response->body());
                    return false;
                }

                $errorCode = $response->json('error_code');
                if ($errorCode !== '000') {
                    Log::error("[SMS] KudiSMS error {$errorCode} sending to {$phone}: " . $response->body());
                    return false;
                }

            } else {
                Log::warning("[SMS] Unknown provider: {$provider}");
                return false;
            }

            Log::info("[SMS] Sent to {$phone} via {$provider}");
            return true;
        } catch (\Throwable $e) {
            Log::error("[SMS] Exception sending to {$phone}: " . $e->getMessage());
            return false;
        }
    }
}
