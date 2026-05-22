<?php

namespace App\Filament\Resources\MeasurementFieldResource\Pages;

use App\Filament\Resources\MeasurementFieldResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMeasurementField extends EditRecord
{
    protected static string $resource = MeasurementFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->disabled(fn ($record) => $record->is_system),
        ];
    }
}
