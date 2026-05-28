<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MeasurementField;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeasurementFieldPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MeasurementField');
    }

    public function view(AuthUser $authUser, MeasurementField $measurementField): bool
    {
        return $authUser->can('View:MeasurementField');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MeasurementField');
    }

    public function update(AuthUser $authUser, MeasurementField $measurementField): bool
    {
        return $authUser->can('Update:MeasurementField');
    }

    public function delete(AuthUser $authUser, MeasurementField $measurementField): bool
    {
        return $authUser->can('Delete:MeasurementField');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MeasurementField');
    }

    public function restore(AuthUser $authUser, MeasurementField $measurementField): bool
    {
        return $authUser->can('Restore:MeasurementField');
    }

    public function forceDelete(AuthUser $authUser, MeasurementField $measurementField): bool
    {
        return $authUser->can('ForceDelete:MeasurementField');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MeasurementField');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MeasurementField');
    }

    public function replicate(AuthUser $authUser, MeasurementField $measurementField): bool
    {
        return $authUser->can('Replicate:MeasurementField');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MeasurementField');
    }

}