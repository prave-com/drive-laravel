<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\FileBookmark;
use App\Models\Folder;
use App\Models\FolderBookmark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $file;

    protected $folder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->rootFolder = Folder::factory()->create([
            'name' => $this->user->id,
            'user_id' => $this->user,
        ]);

        $this->folder = Folder::factory()->create([
            'folder_id' => $this->rootFolder,
            'user_id' => $this->user,
        ]);

        $this->file = File::factory()->create([
            'folder_id' => $this->rootFolder,
            'user_id' => $this->user,
        ]);
    }

    public function test_user_can_bookmark_file()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('file-bookmarks.store', $this->file), [
                'is_starred' => true,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'File bookmarked successfully.');

        $this->assertDatabaseHas('file_bookmarks', [
            'file_id' => $this->file->id,
            'user_id' => $this->user->id,
            'is_starred' => true,
        ]);
    }

    public function test_user_can_unbookmark_file()
    {
        FileBookmark::factory()->bookmarked()->create([
            'file_id' => $this->file,
            'user_id' => $this->user,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->post(route('file-bookmarks.store', $this->file), [
                'is_starred' => false,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'File unbookmarked successfully.');

        $this->assertDatabaseHas('file_bookmarks', [
            'file_id' => $this->file->id,
            'user_id' => $this->user->id,
            'is_starred' => false,
        ]);
    }

    public function test_user_can_bookmark_folder()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('folder-bookmarks.store', $this->folder), [
                'is_starred' => true,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Folder bookmarked successfully.');

        $this->assertDatabaseHas('folder_bookmarks', [
            'folder_id' => $this->folder->id,
            'user_id' => $this->user->id,
            'is_starred' => true,
        ]);
    }

    public function test_user_can_unbookmark_folder()
    {
        FolderBookmark::factory()->bookmarked()->create([
            'folder_id' => $this->folder,
            'user_id' => $this->user,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->post(route('folder-bookmarks.store', $this->folder), [
                'is_starred' => false,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Folder unbookmarked successfully.');

        $this->assertDatabaseHas('folder_bookmarks', [
            'folder_id' => $this->folder->id,
            'user_id' => $this->user->id,
            'is_starred' => false,
        ]);
    }

    public function test_unauthenticated_user_cannot_bookmark()
    {
        $fileResponse = $this
            ->post(route('file-bookmarks.store', $this->file), [
                'is_starred' => true,
            ]);

        $folderResponse = $this
            ->post(route('folder-bookmarks.store', $this->rootFolder), [
                'is_starred' => true,
            ]);

        $fileResponse->assertRedirect(route('login'));
        $folderResponse->assertRedirect(route('login'));
    }
}
