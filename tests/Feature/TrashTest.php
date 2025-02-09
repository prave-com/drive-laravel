<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TrashTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->user = User::factory()->create();
        $this->rootFolder = Folder::factory()->create([
            'name' => $this->user->id,
            'user_id' => $this->user->id,
        ]);
    }

    // public function test_user_can_view_trash_page()
    // {
    //     $this
    //         ->actingAs($this->user)
    //         ->get(route('trash.index'))
    //          ->assertOk()
    //         ->assertViewIs('trash.index')
    //         ->assertViewHas(['deletedFolders', 'deletedFiles', 'isSuperAdmin']);
    // }

    public function test_user_can_delete_file()
    {
        $file = File::factory()->create([
            'folder_id' => $this->rootFolder->id,
            'user_id' => $this->user->id,
        ]);

        Storage::put("drive{$file->full_path}", 'content');

        $response = $this
            ->actingAs($this->user)
            ->delete(route('files.destroy', $file));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'File deleted successfully.');

        $this->assertSoftDeleted($file);

        Storage::disk('local')->assertExists("drive{$file->full_path}");

        $this
            ->actingAs($this->user)
            ->get(route('trash.index'))
            ->assertSee($file->name);
    }

    public function test_user_can_delete_folder()
    {
        $folder = Folder::factory()->create([
            'folder_id' => $this->rootFolder->id,
            'user_id' => $this->user->id,
        ]);

        Storage::makeDirectory("drive{$folder->full_path}");

        $response = $this
            ->actingAs($this->user)
            ->delete(route('folders.destroy', $folder));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Folder deleted successfully.');

        $this->assertSoftDeleted($folder);

        Storage::disk('local')->assertExists("drive{$folder->full_path}");

        $this
            ->actingAs($this->user)
            ->get(route('trash.index'))
            ->assertSee($folder->name);
    }

    public function test_user_can_restore_file()
    {
        $file = File::factory()->create([
            'folder_id' => $this->rootFolder->id,
            'user_id' => $this->user->id,
        ]);

        Storage::put("drive{$file->full_path}", 'content');

        $file->delete();

        $this
            ->actingAs($this->user)
            ->get(route('trash.index'))
            ->assertSee($file->name);

        $response = $this
            ->actingAs($this->user)
            ->post(route('trash.files.restore', $file));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'File restored successfully.');

        $this->assertNotSoftDeleted($file);

        Storage::disk('local')->assertExists("drive{$file->full_path}");
    }

    public function test_user_can_restore_folder()
    {
        $folder = Folder::factory()->create([
            'folder_id' => $this->rootFolder->id,
            'user_id' => $this->user->id,
        ]);

        Storage::makeDirectory("drive{$folder->full_path}");

        $folder->delete();

        $this
            ->actingAs($this->user)
            ->get(route('trash.index'))
            ->assertSee($folder->name);

        $response = $this
            ->actingAs($this->user)
            ->post(route('trash.folders.restore', $folder));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Folder restored successfully.');

        $this->assertNotSoftDeleted($folder);

        Storage::disk('local')->assertExists("drive{$folder->full_path}");
    }

    public function test_user_can_permanent_delete_file()
    {
        $file = File::factory()->create([
            'folder_id' => $this->rootFolder->id,
            'user_id' => $this->user->id,
        ]);

        Storage::put("drive{$file->full_path}", 'content');

        $file->delete();

        $this
            ->actingAs($this->user)
            ->get(route('trash.index'))
            ->assertSee($file->name);

        $response = $this
            ->actingAs($this->user)
            ->delete(route('trash.files.force-delete', $file));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'File permanently deleted.');

        Storage::disk('local')->assertMissing("drive{$file->full_path}");
    }

    public function test_user_can_permanent_delete_folder()
    {
        $folder = Folder::factory()->create([
            'folder_id' => $this->rootFolder->id,
            'user_id' => $this->user->id,
        ]);

        Storage::makeDirectory("drive{$folder->full_path}");

        $folder->delete();

        $this
            ->actingAs($this->user)
            ->get(route('trash.index'))
            ->assertSee($folder->name);

        $response = $this
            ->actingAs($this->user)
            ->delete(route('trash.folders.force-delete', $folder));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Folder permanently deleted.');

        Storage::disk('local')->assertMissing("drive{$folder->full_path}");
    }

    public function test_unauthorized_user_cannot_access_trash()
    {
        $this
            ->get(route('trash.index'))
            ->assertRedirect('/login');
    }

    public function test_user_cannot_restore_other_users_folder()
    {
        $otherUser = User::factory()->create();
        $folder = Folder::factory()->create([
            'folder_id' => $this->rootFolder->id,
            'user_id' => $otherUser->id,
        ]);

        $folder->delete();

        $response = $this
            ->actingAs($this->user)
            ->post(route('trash.folders.restore', $folder));

        $response->assertForbidden();
    }

    public function test_user_cannot_restore_other_users_file()
    {
        $otherUser = User::factory()->create();
        $file = File::factory()->create([
            'folder_id' => $this->rootFolder->id,
            'user_id' => $otherUser->id,
        ]);

        $file->delete();

        $response = $this
            ->actingAs($this->user)
            ->post(route('trash.files.restore', $file));

        $response->assertForbidden();
    }
}
