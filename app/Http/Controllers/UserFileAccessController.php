<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Http\Requests\DeleteUserFileAccessRequest;
use App\Http\Requests\StoreUserFileAccessRequest;
use App\Http\Requests\UpdateUserFileAccessRequest;
use App\Models\File;
use App\Models\User;
use App\Models\UserFileAccess;
use Illuminate\Support\Facades\Auth;

class UserFileAccessController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserFileAccessRequest $request, File $file)
    {
        $permissionType = $request->enum('permission_type', PermissionType::class);
        $targetUser = User::where('email', $request->email)->first();

        $userFileAccess = UserFileAccess::create([
            'permission_type' => $permissionType,
            'file_id' => $file->id,
            'user_id' => $targetUser->id,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($userFileAccess)
            ->event('created')
            ->log("Grant pengguna {$targetUser->email} permission {$permissionType->name} untuk file {$file->name}");

        return back()->with('success', 'Access granted successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserFileAccessRequest $request, UserFileAccess $userFileAccess)
    {
        $permissionType = $request->enum('permission_type', PermissionType::class);
        $userFileAccess->update([
            'permission_type' => $permissionType,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($userFileAccess)
            ->withProperties(['permission_type' => $permissionType])
            ->event('updated')
            ->log("Grant pengguna {$userFileAccess->owner->email} permission {$permissionType->name} untuk file {$userFileAccess->file->name}");

        return back()->with('success', 'Access updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteUserFileAccessRequest $request, UserFileAccess $userFileAccess)
    {
        $userFileAccess->delete();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($userFileAccess)
            ->event('deleted')
            ->log("Delete permission pengguna {$userFileAccess->owner->email} untuk file {$userFileAccess->file->name}");

        return back()->with('success', 'Access Deleted successfully.');
    }
}
