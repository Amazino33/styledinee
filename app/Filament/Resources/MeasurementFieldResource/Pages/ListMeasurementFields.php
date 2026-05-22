<?php

namespace App\Filament\Resources\MeasurementFieldResource\Pages;

use App\Filament\Resources\MeasurementFieldResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMeasurementFields extends ListRecords
{
    protected static string $resource = MeasurementFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
