<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message via WAWP.
     *
     * Returns true if the message was dispatched, false if skipped (not enabled / no credentials).
     */
    public function send(string $phone, string $message): bool
    {
        $enabled    = AppSetting::bool('wawp_enabled', false);
        $instanceId = AppSetting::get('wawp_instance_id', '');
        $token      = AppSetting::get('wawp_access_token', '');

        if (! $enabled || empty($instanceId) || empty($token)) {
            Log::info("[WAWP stub] Would send to {$phone}: {$message}");
            return false;
        }

        // WAWP requires digits only, no spaces or dashes
        $number = preg_replace('/\D/', '', $phone);

        try {
            $response = Http::post('https://app.wawp.net/api/send', [
                'number'       => $number,
                'type'         => 'text',
                'message'      => $message,
                'instance_id'  => $instanceId,
                'access_token' => $token,
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
}
