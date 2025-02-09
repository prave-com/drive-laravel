<?php

namespace Tests\Feature;

use App\Enums\PermissionType;
use App\Models\Folder;
use App\Models\User;
use App\Models\UserFolderAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFolderAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test granting access to a folder with valid inputs.
     */
    public function test_user_can_grant_access_to_folder_with_valid_email_laravel()
    {
        $owner = User::factory()->create();
        $targetUser = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)
            ->post(route('folder-accesses.store', $folder), [
                'email' => $targetUser->email,
                'permission_type' => PermissionType::READ->value,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Access granted successfully.');

        $this->assertDatabaseHas('user_folder_accesses', [
            'folder_id' => $folder->id,
            'user_id' => $targetUser->id,
            'permission_type' => PermissionType::READ->value,
        ]);
    }

    /**
     * Test granting access with an existing access.
     */
    public function test_user_cannot_grant_access_if_folder_access_already_exists()
    {
        $owner = User::factory()->create();
        $targetUser = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $owner->id]);

        UserFolderAccess::factory()->create([
            'folder_id' => $folder->id,
            'user_id' => $targetUser->id,
            'permission_type' => PermissionType::READ,
        ]);

        $response = $this->actingAs($owner)
            ->post(route('folder-accesses.store', $folder), [
                'email' => $targetUser->email,
                'permission_type' => PermissionType::READ->value,
            ]);

        $response->assertSessionHasErrors([
            'email' => trans('validation.unique_user_folder_access', [
                'name' => $targetUser->email,
            ]),
        ], null, 'grant_access_user_folder_'.$folder->id);

        $this->assertDatabaseCount('user_folder_accesses', 1);
    }

    /**
     * Test granting access to the folder owner.
     */
    public function test_user_cannot_grant_access_to_folder_owner()
    {
        $owner = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)
            ->post(route('folder-accesses.store', $folder), [
                'email' => $owner->email,
                'permission_type' => PermissionType::READ->value,
            ]);

        $response->assertSessionHasErrors([
            'email' => trans('validation.cannot_add_folder_owner_to_access'),
        ], null, 'grant_access_user_folder_'.$folder->id);

        $this->assertDatabaseMissing('user_folder_accesses', [
            'folder_id' => $folder->id,
            'user_id' => $owner->id,
        ]);
    }

    /**
     * Test updating folder access.
     */
    public function test_user_can_update_folder_access_permission()
    {
        $owner = User::factory()->create();
        $targetUser = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $owner->id]);
        $userFolderAccess = UserFolderAccess::factory()->create([
            'folder_id' => $folder->id,
            'user_id' => $targetUser->id,
            'permission_type' => PermissionType::READ,
        ]);

        $response = $this->actingAs($owner)
            ->patch(route('folder-accesses.update', $userFolderAccess), [
                'permission_type' => PermissionType::READ_WRITE->value,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Access updated successfully.');

        $this->assertDatabaseHas('user_folder_accesses', [
            'id' => $userFolderAccess->id,
            'permission_type' => PermissionType::READ_WRITE->value,
        ]);
    }

    /**
     * Test removing folder access.
     */
    public function test_user_can_remove_folder_access()
    {
        $owner = User::factory()->create();
        $targetUser = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $owner->id]);
        $userFolderAccess = UserFolderAccess::factory()->create([
            'folder_id' => $folder->id,
            'user_id' => $targetUser->id,
        ]);

        $response = $this->actingAs($owner)
            ->delete(route('folder-accesses.destroy', $userFolderAccess));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Access Deleted successfully.');

        $this->assertDatabaseMissing('user_folder_accesses', [
            'id' => $userFolderAccess->id,
        ]);
    }

    /**
     * Test that only the folder owner can manage folder access.
     */
    public function test_only_folder_owner_can_manage_folder_access()
    {
        $owner = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $owner->id]);
        $otherUser = User::factory()->create();
        $targetUser = User::factory()->create();

        $response = $this
            ->actingAs($otherUser)
            ->post(route('folder-accesses.store', $folder->id), [
                'email' => $targetUser->email,
                'permission_type' => PermissionType::READ->value,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('user_folder_accesses', [
            'folder_id' => $folder->id,
            'user_id' => $targetUser->id,
        ]);
    }
}
