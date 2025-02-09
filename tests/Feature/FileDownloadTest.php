<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_download_file()
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $rootFolder = Folder::factory()->create([
            'name' => $user->id,
            'user_id' => $user->id,
        ]);

        $file = File::factory()->create([
            'name' => 'test.txt',
            'folder_id' => $rootFolder->id,
            'user_id' => $user->id,
        ]);

        Storage::put("drive{$file->full_path}", 'content');

        $response = $this
            ->actingAs($user)
            ->get(route('files.download', $file));

        $response->assertOk();
        $response->assertDownload('test.txt');
    }

    public function test_unauthenticated_user_cannot_download_file()
    {
        $file = File::factory()->create();

        $response = $this->get(route('files.download', $file));

        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_download_file_they_do_not_own()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $rootFolder = Folder::factory()->create([
            'name' => $user->id,
            'user_id' => $user->id,
        ]);

        $file = File::factory()->create([
            'name' => 'test.txt',
            'folder_id' => $rootFolder->id,
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($otherUser)
            ->get(route('files.download', $file));

        $response->assertForbidden();
    }
}
