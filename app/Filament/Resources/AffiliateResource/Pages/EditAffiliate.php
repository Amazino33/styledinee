<?php

namespace App\Filament\Resources\AffiliateResource\Pages;

use App\Filament\Resources\AffiliateResource;
use App\Models\Username;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAffiliate extends EditRecord
{
    protected static string $resource = AffiliateResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function afterSave(): void
    {
        // If the username changed, update the global registry
        $record = $this->record;
        $old    = $record->getOriginal('username');

        if ($old && $old !== $record->username) {
            Username::transfer($old, $record->username);
        }
    }
}
