<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Enums\UserRole;
use App\Models\File;
use App\Models\User;

class FilePolicy
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
    public function view(User $user, File $file): bool
    {
        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $file->owner->id) {
            return true;
        }

        if ($file->permission_type === PermissionType::READ || $file->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        $userFileAccess = $file->userFileAccesses()->firstWhere('user_id', $user->id);
        if ($userFileAccess && ($userFileAccess->permission_type === PermissionType::READ || $userFileAccess->permission_type === PermissionType::READ_WRITE)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, File $file): bool
    {
        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $file->owner->id) {
            return true;
        }

        if ($file->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        $userFileAccess = $file->userFileAccesses()->firstWhere('user_id', $user->id);
        if ($userFileAccess && $userFileAccess->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update access models.
     */
    public function grantAccess(User $user, File $file): bool
    {
        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $file->owner->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, File $file): bool
    {
        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $file->owner->id) {
            return true;
        }

        if ($file->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        $userFileAccess = $file->userFileAccesses()->firstWhere('user_id', $user->id);
        if ($userFileAccess && $userFileAccess->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, File $file): bool
    {
        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $file->owner->id) {
            return true;
        }

        if ($file->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        $userFileAccess = $file->userFileAccesses()->firstWhere('user_id', $user->id);
        if ($userFileAccess && $userFileAccess->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, File $file): bool
    {
        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $file->owner->id) {
            return true;
        }

        if ($file->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        $userFileAccess = $file->userFileAccesses()->firstWhere('user_id', $user->id);
        if ($userFileAccess && $userFileAccess->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, File $file): bool
    {
        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $file->owner->id) {
            return true;
        }

        if ($file->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        $userFileAccess = $file->userFileAccesses()->firstWhere('user_id', $user->id);
        if ($userFileAccess && $userFileAccess->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        return false;
    }
}
