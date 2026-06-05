<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $roles = $data['roles'] ?? ['cashier'];
        unset($data['roles']);

        $user = static::getModel()::create($data);
        $user->syncRoles($roles);

        return $user;
    }
}
