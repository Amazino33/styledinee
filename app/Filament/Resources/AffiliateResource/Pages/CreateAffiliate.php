<?php

namespace App\Filament\Resources\AffiliateResource\Pages;

use App\Filament\Resources\AffiliateResource;
use App\Models\Username;
use Filament\Resources\Pages\CreateRecord;

class CreateAffiliate extends CreateRecord
{
    protected static string $resource = AffiliateResource::class;

    protected function afterCreate(): void
    {
        // Register username in the global registry
        Username::claim($this->record->username, 'affiliate', $this->record->id);
    }
}
