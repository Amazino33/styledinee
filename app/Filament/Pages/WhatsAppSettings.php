<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use App\Services\WhatsAppService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class WhatsAppSettings extends Page
{
    protected string $view = 'filament.pages.whatsapp-settings';
    protected static ?string $navigationLabel = 'WhatsApp';
    protected static ?string $title = 'WhatsApp Settings';
    protected static ?int $navigationSort = 20;

    public static function getNavigationIcon(): string { return 'heroicon-o-chat-bubble-left-ellipsis'; }
    public static function getNavigationGroup(): ?string { return 'Administration'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    // ── Form state ─────────────────────────────────────────────────────────────

    public string $instance_id    = '';
    public string $access_token   = '';
    public bool   $enabled        = false;
    public string $test_number    = '';

    public function mount(): void
    {
        $this->instance_id  = AppSetting::get('wawp_instance_id', '');
        $this->access_token = AppSetting::get('wawp_access_token', '');
        $this->enabled      = AppSetting::bool('wawp_enabled', false);
    }

    // ── Actions ────────────────────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate([
            'instance_id'  => ['nullable', 'string', 'max:100'],
            'access_token' => ['nullable', 'string', 'max:200'],
        ]);

        AppSetting::set('wawp_instance_id',  $this->instance_id);
        AppSetting::set('wawp_access_token', $this->access_token);
        AppSetting::set('wawp_enabled',      $this->enabled ? '1' : '0');

        Notification::make()->title('Settings saved.')->success()->send();
    }

    public function sendTest(): void
    {
        $this->validate([
            'test_number' => ['required', 'string', 'min:7'],
        ]);

        $sent = app(WhatsAppService::class)->send(
            $this->test_number,
            'This is a test message from Styledinee. Your WhatsApp integration is working correctly.'
        );

        if ($sent) {
            Notification::make()->title('Test message sent.')->success()->send();
        } else {
            Notification::make()
                ->title('Message not sent.')
                ->body('Check that WhatsApp is enabled and credentials are saved.')
                ->warning()
                ->send();
        }
    }
}
