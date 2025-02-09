<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Http\Requests\DeleteUserFolderAccessRequest;
use App\Http\Requests\StoreUserFolderAccessRequest;
use App\Http\Requests\UpdateUserFolderAccessRequest;
use App\Models\Folder;
use App\Models\User;
use App\Models\UserFolderAccess;
use Illuminate\Support\Facades\Auth;

class UserFolderAccessController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserFolderAccessRequest $request, Folder $folder)
    {
        $permissionType = $request->enum('permission_type', PermissionType::class);
        $targetUser = User::where('email', $request->email)->first();

        $userFolderAccess = UserFolderAccess::create([
            'permission_type' => $permissionType,
            'folder_id' => $folder->id,
            'user_id' => $targetUser->id,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($userFolderAccess)
            ->event('created')
            ->log("Grant pengguna {$targetUser->email} permission {$permissionType->name} untuk folder {$folder->name}");

        return back()->with('success', 'Access granted successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserFolderAccessRequest $request, UserFolderAccess $userFolderAccess)
    {
        $permissionType = $request->enum('permission_type', PermissionType::class);
        $userFolderAccess->update([
            'permission_type' => $permissionType,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($userFolderAccess)
            ->withProperties(['permission_type' => $permissionType])
            ->event('updated')
            ->log("Grant pengguna {$userFolderAccess->owner->email} permission {$permissionType->name} untuk folder {$userFolderAccess->folder->name}");

        return back()->with('success', 'Access updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteUserFolderAccessRequest $request, UserFolderAccess $userFolderAccess)
    {
        $userFolderAccess->delete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($userFolderAccess)
            ->event('deleted')
            ->log("Delete permission pengguna {$userFolderAccess->owner->email} untuk folder {$userFolderAccess->folder->name}");

        return back()->with('success', 'Access Deleted successfully.');
    }
}
