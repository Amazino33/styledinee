<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\OrderStatusLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderStatusLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OrderStatusLog');
    }

    public function view(AuthUser $authUser, OrderStatusLog $orderStatusLog): bool
    {
        return $authUser->can('View:OrderStatusLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OrderStatusLog');
    }

    public function update(AuthUser $authUser, OrderStatusLog $orderStatusLog): bool
    {
        return $authUser->can('Update:OrderStatusLog');
    }

    public function delete(AuthUser $authUser, OrderStatusLog $orderStatusLog): bool
    {
        return $authUser->can('Delete:OrderStatusLog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:OrderStatusLog');
    }

    public function restore(AuthUser $authUser, OrderStatusLog $orderStatusLog): bool
    {
        return $authUser->can('Restore:OrderStatusLog');
    }

    public function forceDelete(AuthUser $authUser, OrderStatusLog $orderStatusLog): bool
    {
        return $authUser->can('ForceDelete:OrderStatusLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:OrderStatusLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:OrderStatusLog');
    }

    public function replicate(AuthUser $authUser, OrderStatusLog $orderStatusLog): bool
    {
        return $authUser->can('Replicate:OrderStatusLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:OrderStatusLog');
    }

}