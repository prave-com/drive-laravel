<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Enums\UserRole;
use App\Models\Folder;
use App\Models\User;

class FolderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Folder $folder): bool
    {
        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $folder->owner->id) {
            return true;
        }

        if ($folder->permission_type === PermissionType::READ || $folder->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        $userFolderAccess = $folder->userFolderAccesses()->firstWhere('user_id', $user->id);
        if ($userFolderAccess && ($userFolderAccess->permission_type === PermissionType::READ || $userFolderAccess->permission_type === PermissionType::READ_WRITE)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Folder $folder): bool
    {
        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $folder->owner->id) {
            return true;
        }

        if ($folder->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        $userFolderAccess = $folder->userFolderAccesses()->firstWhere('user_id', $user->id);
        if ($userFolderAccess && $userFolderAccess->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update access models.
     */
    public function grantAccess(User $user, Folder $folder): bool
    {
        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $folder->owner->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Folder $folder): bool
    {
        if ($folder->is_root) {
            return false;
        }

        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $folder->owner->id) {
            return true;
        }

        if ($folder->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        $userFolderAccess = $folder->userFolderAccesses()->firstWhere('user_id', $user->id);
        if ($userFolderAccess && $userFolderAccess->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can modify content of the model.
     */
    public function modifyContent(User $user, Folder $folder): bool
    {
        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $folder->owner->id) {
            return true;
        }

        if ($folder->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        $userFolderAccess = $folder->userFolderAccesses()->firstWhere('user_id', $user->id);
        if ($userFolderAccess && $userFolderAccess->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Folder $folder): bool
    {
        if ($folder->is_root) {
            return false;
        }

        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $folder->owner->id) {
            return true;
        }

        if ($folder->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        $userFolderAccess = $folder->userFolderAccesses()->firstWhere('user_id', $user->id);
        if ($userFolderAccess && $userFolderAccess->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Folder $folder): bool
    {
        if ($folder->is_root) {
            return false;
        }

        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $folder->owner->id) {
            return true;
        }

        if ($folder->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        $userFolderAccess = $folder->userFolderAccesses()->firstWhere('user_id', $user->id);
        if ($userFolderAccess && $userFolderAccess->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Folder $folder): bool
    {
        if ($folder->is_root) {
            return false;
        }

        if ($user->role === UserRole::SUPERADMIN) {
            return true;
        }

        if ($user->id === $folder->owner->id) {
            return true;
        }

        if ($folder->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        $userFolderAccess = $folder->userFolderAccesses()->firstWhere('user_id', $user->id);
        if ($userFolderAccess && $userFolderAccess->permission_type === PermissionType::READ_WRITE) {
            return true;
        }

        return false;
    }
}
