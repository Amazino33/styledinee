<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;

class EditProfile extends \Filament\Auth\Pages\EditProfile
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('back_to_dashboard')
                ->label('← Dashboard')
                ->url(fn () => $this->getPanel()->getUrl())
                ->color('gray')
                ->outlined(),
        ];
    }
}
