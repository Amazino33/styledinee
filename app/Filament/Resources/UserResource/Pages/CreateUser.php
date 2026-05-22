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
        $role = $data['role'] ?? 'cashier';
        unset($data['role']);

        $user = static::getModel()::create($data);
        $user->syncRoles([$role]);

        return $user;
    }
}
