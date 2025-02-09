<?php

namespace App\Policies;

use App\Enums\StorageRequestStatus;
use App\Enums\UserRole;
use App\Models\StorageRequest;
use App\Models\User;

class StorageRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StorageRequest $storageRequest): bool
    {
        if ($user->role === UserRole::SUPERADMIN || $user->role === UserRole::ADMIN) {
            return true;
        }

        if ($user->id === $storageRequest->owner->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->role === UserRole::USER) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StorageRequest $storageRequest): bool
    {
        if ($storageRequest->status !== StorageRequestStatus::PENDING) {
            return false;
        }

        if ($user->role === UserRole::SUPERADMIN || $user->role === UserRole::ADMIN) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StorageRequest $storageRequest): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StorageRequest $storageRequest): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StorageRequest $storageRequest): bool
    {
        //
    }
}
