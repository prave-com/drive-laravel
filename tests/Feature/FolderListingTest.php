<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FolderListingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->rootFolder = Folder::factory()->create([
            'name' => $this->user->id,
            'user_id' => $this->user,
        ]);
    }

    public function test_regular_user_redirected_to_their_root_folder()
    {
        $response = $this
            ->actingAs($this->user)
            ->get(route('folders.index'));

        $response->assertRedirect(route('folders.show', $this->rootFolder));
    }

    public function test_unauthorized_user_cannot_view_others_folders()
    {
        $otherUser = User::factory()->create();
        $folder = Folder::factory()->create(['user_id' => $otherUser->id]);

        $response = $this
            ->actingAs($this->user)
            ->get(route('folders.show', $folder));

        $response->assertForbidden();
    }

    // public function test_user_can_view_folder()
    // {
    //     $folder = Folder::factory()->create([
    //         'folder_id' => $this->rootFolder,
    //         'user_id' => $this->user,
    //     ]);

    //     $response = $this
    //         ->actingAs($this->user)
    //         ->get(route('folders.show', $folder));

    //     $response->getContent();
    //     $response->assertOk();
    //     $response->assertViewHas(['folder' => $folder]);
    // }

    // public function test_user_can_view_nested_folder_structure()
    // {
    //     $folder = Folder::factory()->create([
    //         'folder_id' => $this->rootFolder,
    //         'user_id' => $this->user,
    //     ]);

    //     $response = $this
    //         ->actingAs($this->user)
    //         ->get(route('folders.show', $folder));

    //     $response->assertOk();
    //     $response->assertSee($folder->name);
    // }

    // public function test_user_can_view_folders_and_files_together()
    // {
    //     $folder = Folder::factory()->create([
    //         'folder_id' => $this->rootFolder,
    //         'user_id' => $this->user,
    //     ]);
    //     $file = File::factory()->create(['folder_id' => $folder]);

    //     $response = $this
    //         ->actingAs($this->user)
    //         ->get(route('folders.show', $folder));

    //     $response->assertOk();
    //     $response->assertSee($file->name);
    // }
}
