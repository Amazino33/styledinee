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

        try {
            $response = Http::withToken($token)          // Bearer header
                ->post('https://app.wawp.net/api/send', [
                    'number'       => $number,
                    'type'         => 'text',
                    'message'      => $message,
                    'instance_id'  => $instanceId,
                    'access_token' => $token,             // also in body for providers that need it
                ]);

            if (! $response->successful()) {
                Log::error("[WAWP] Failed to send to {$phone}: " . $response->body());
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("[WAWP] Exception sending to {$phone}: " . $e->getMessage());
            return false;
        }
    }

    private function sendSms(string $phone, string $message): bool
    {
        $enabled    = AppSetting::bool('sms_enabled', false);
        $provider   = AppSetting::get('sms_provider', '');   // 'termii' | 'bulksms'
        $apiKey     = AppSetting::get('sms_api_key', '');
        $senderId   = AppSetting::get('sms_sender_id', 'Styledinee');

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
            } elseif ($provider === 'bulksms') {
                $response = Http::withBasicAuth($apiKey, AppSetting::get('sms_api_secret', ''))
                    ->post('https://api.bulksms.com/v1/messages', [
                        ['to' => $number, 'body' => $message],
                    ]);
            } else {
                Log::warning("[SMS] Unknown provider: {$provider}");
                return false;
            }

            if (! $response->successful()) {
                Log::error("[SMS] Failed to send to {$phone}: " . $response->body());
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
