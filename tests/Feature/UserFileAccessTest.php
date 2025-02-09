<?php

namespace Tests\Feature;

use App\Enums\PermissionType;
use App\Models\File;
use App\Models\User;
use App\Models\UserFileAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFileAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test granting access to a file with valid inputs.
     */
    public function test_user_can_grant_access_to_file_with_valid_email_laravel()
    {
        $owner = User::factory()->create();
        $targetUser = User::factory()->create();
        $file = File::factory()->create(['user_id' => $owner->id]);

        $response = $this
            ->actingAs($owner)
            ->post(route('file-accesses.store', $file), [
                'email' => $targetUser->email,
                'permission_type' => PermissionType::READ->value,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Access granted successfully.');

        $this->assertDatabaseHas('user_file_accesses', [
            'file_id' => $file->id,
            'user_id' => $targetUser->id,
            'permission_type' => PermissionType::READ->value,
        ]);
    }

    /**
     * Test granting access with an existing access.
     */
    public function test_user_cannot_grant_access_if_file_access_already_exists()
    {
        $owner = User::factory()->create();
        $targetUser = User::factory()->create();
        $file = File::factory()->create(['user_id' => $owner->id]);

        UserFileAccess::factory()->create([
            'file_id' => $file->id,
            'user_id' => $targetUser->id,
            'permission_type' => PermissionType::READ,
        ]);

        $response = $this
            ->actingAs($owner)
            ->post(route('file-accesses.store', $file), [
                'email' => $targetUser->email,
                'permission_type' => PermissionType::READ->value,
            ]);

        $response->assertSessionHasErrors([
            'email' => trans('validation.unique_user_file_access', [
                'name' => $targetUser->email,
            ]),
        ], null, 'grant_access_user_file_'.$file->id);

        $this->assertDatabaseCount('user_file_accesses', 1);
    }

    /**
     * Test granting access to the file owner.
     */
    public function test_user_cannot_grant_access_to_file_owner()
    {
        $owner = User::factory()->create();
        $file = File::factory()->create(['user_id' => $owner->id]);

        $response = $this
            ->actingAs($owner)
            ->post(route('file-accesses.store', $file), [
                'email' => $owner->email,
                'permission_type' => PermissionType::READ->value,
            ]);

        $response->assertSessionHasErrors([
            'email' => trans('validation.cannot_add_file_owner_to_access'),
        ], null, 'grant_access_user_file_'.$file->id);

        $this->assertDatabaseMissing('user_file_accesses', [
            'file_id' => $file->id,
            'user_id' => $owner->id,
        ]);
    }

    /**
     * Test updating file access.
     */
    public function test_user_can_update_file_access_permission()
    {
        $owner = User::factory()->create();
        $targetUser = User::factory()->create();
        $file = File::factory()->create(['user_id' => $owner->id]);
        $userFileAccess = UserFileAccess::factory()->create([
            'file_id' => $file->id,
            'user_id' => $targetUser->id,
            'permission_type' => PermissionType::READ,
        ]);

        $response = $this
            ->actingAs($owner)
            ->patch(route('file-accesses.update', $userFileAccess), [
                'permission_type' => PermissionType::READ_WRITE->value,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Access updated successfully.');

        $this->assertDatabaseHas('user_file_accesses', [
            'id' => $userFileAccess->id,
            'permission_type' => PermissionType::READ_WRITE->value,
        ]);
    }

    /**
     * Test removing file access.
     */
    public function test_user_can_remove_file_access()
    {
        $owner = User::factory()->create();
        $targetUser = User::factory()->create();
        $file = File::factory()->create(['user_id' => $owner->id]);
        $userFileAccess = UserFileAccess::factory()->create([
            'file_id' => $file->id,
            'user_id' => $targetUser->id,
        ]);

        $response = $this
            ->actingAs($owner)
            ->delete(route('file-accesses.destroy', $userFileAccess));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Access Deleted successfully.');

        $this->assertDatabaseMissing('user_file_accesses', [
            'id' => $userFileAccess->id,
        ]);
    }

    /**
     * Test that only the file owner can manage file access.
     */
    public function test_only_file_owner_can_manage_file_access()
    {
        $owner = User::factory()->create();
        $file = File::factory()->create(['user_id' => $owner->id]);
        $otherUser = User::factory()->create();
        $targetUser = User::factory()->create();

        $response = $this
            ->actingAs($otherUser)
            ->post(route('file-accesses.store', $file), [
                'email' => $otherUser->email,
                'permission_type' => PermissionType::READ->value,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('user_file_accesses', [
            'file_id' => $file->id,
            'user_id' => $targetUser->id,
        ]);
    }
}
