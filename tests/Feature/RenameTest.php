<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RenameTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->rootFolder = Folder::factory()->create([
            'name' => $this->user->id,
            'user_id' => $this->user->id,
        ]);
        $this->folder = Folder::factory()->create([
            'folder_id' => $this->rootFolder->id,
            'user_id' => $this->user->id,
        ]);
        $this->file = File::factory()->create([
            'folder_id' => $this->folder->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_cannot_rename_root_folder()
    {
        $response = $this
            ->actingAs($this->user)
            ->patch(route('folders.update', $this->rootFolder), [
                'name' => 'New Folder',
            ]);

        $response->assertForbidden();
    }

    public function test_user_can_rename_folder_with_valid_name()
    {
        Storage::fake('local');

        Storage::makeDirectory("drive{$this->folder->full_path}");

        $response = $this
            ->actingAs($this->user)
            ->patch(route('folders.update', $this->folder), [
                'name' => 'Renamed Folder',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Folder updated successfully.');

        Storage::disk('local')->assertMissing("drive{$this->folder->full_path}");
        Storage::disk('local')->assertExists("drive{$this->folder->refresh()->full_path}");

        $this->assertDatabaseHas('folders', [
            'id' => $this->folder->id,
            'name' => 'Renamed Folder',
        ]);
    }

    public function test_user_cannot_rename_folder_to_name_longer_than_255_characters()
    {
        $response = $this
            ->actingAs($this->user)
            ->patch(route('folders.update', $this->folder), [
                'name' => str_repeat('a', 256),
            ]);

        $response->assertSessionHasErrors([
            'name' => trans('validation.max.string', [
                'attribute' => 'name',
                'max' => '255',
            ]),
        ], null, 'rename_folder_'.$this->folder->id);
    }

    public function test_user_cannot_rename_folder_to_empty_name()
    {
        $response = $this
            ->actingAs($this->user)
            ->patch(route('folders.update', $this->folder), [
                'name' => '',
            ]);

        $response->assertSessionHasErrors([
            'name' => trans('validation.required', [
                'attribute' => 'name',
            ]),
        ], null, 'rename_folder_'.$this->folder->id);
    }

    public function test_user_cannot_rename_folder_with_non_ascii_characters()
    {
        $response = $this
            ->actingAs($this->user)
            ->patch(route('folders.update', $this->folder), [
                'name' => 'FolderÑonAscii',
            ]);

        $response->assertSessionHasErrors([
            'name' => trans('validation.ascii', [
                'attribute' => 'name',
            ]),
        ], null, 'rename_folder_'.$this->folder->id);
    }

    public function test_user_cannot_rename_folder_with_invalid_characters()
    {
        $validName = 'InvalidFolderName.txt';

        $invalidChars = ['\\', '/', ':', '*', '?', '"', '<', '>', '|'];

        foreach ($invalidChars as $char) {
            $invalidName = str_replace('Name', $char.'Name', $validName);

            $response = $this
                ->actingAs($this->user)
                ->patch(route('folders.update', $this->folder), [
                    'name' => $invalidName,
                ]);

            $response->assertSessionHasErrors([
                'name' => trans('validation.regex', [
                    'attribute' => 'name',
                ]),
            ], null, 'rename_folder_'.$this->folder->id);
        }
    }

    public function test_user_cannot_rename_folder_to_name_already_used_in_same_folder_by_file()
    {
        $otherFolder = Folder::factory()->create([
            'folder_id' => $this->folder->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->patch(route('folders.update', $otherFolder), [
                'name' => $this->file->name,
            ]);

        $response->assertSessionHasErrors([
            'name' => trans('validation.unique_folder_name', [
                'name' => $this->file->name,
            ]),
        ], null, 'rename_folder_'.$otherFolder->id);
    }

    public function test_user_cannot_rename_folder_to_name_already_used_by_another_folder_in_same_folder()
    {
        $otherFolder = Folder::factory()->create([
            'folder_id' => $this->rootFolder->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->patch(route('folders.update', $otherFolder), [
                'name' => $this->folder->name,
            ]);

        $response->assertSessionHasErrors([
            'name' => trans('validation.unique', [
                'attribute' => 'name',
            ]),
        ], null, 'rename_folder_'.$otherFolder->id);
    }

    public function test_user_can_only_rename_folders_they_own()
    {
        $other = User::factory()->create();

        $response = $this
            ->actingAs($other)
            ->patch(route('folders.update', $this->folder), [
                'name' => 'Unauthorized Rename',
            ]);

        $response->assertForbidden();
    }

    public function test_user_can_rename_file_with_valid_name()
    {
        Storage::fake('local');

        Storage::put("drive{$this->file->full_path}", 'content');

        $response = $this
            ->actingAs($this->user)
            ->patch(route('files.update', $this->file), [
                'name' => 'Renamed File.txt',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'File updated successfully.');

        Storage::disk('local')->assertMissing("drive{$this->file->full_path}");
        Storage::disk('local')->assertExists("drive{$this->file->refresh()->full_path}");

        $this->assertDatabaseHas('files', [
            'id' => $this->file->id,
            'name' => 'Renamed File.txt',
        ]);
    }

    public function test_user_cannot_rename_file_to_name_longer_than_255_characters()
    {
        $response = $this
            ->actingAs($this->user)
            ->patch(route('files.update', $this->file), [
                'name' => str_repeat('a', 252).'.txt',
            ]);

        $response->assertSessionHasErrors([
            'name' => trans('validation.max.string', [
                'attribute' => 'name',
                'max' => '255',
            ]),
        ], null, 'rename_file_'.$this->file->id);
    }

    public function test_user_cannot_rename_file_to_empty_name()
    {
        $response = $this
            ->actingAs($this->user)
            ->patch(route('files.update', $this->file), [
                'name' => '',
            ]);

        $response->assertSessionHasErrors([
            'name' => trans('validation.required', [
                'attribute' => 'name',
            ]),
        ], null, 'rename_file_'.$this->file->id);
    }

    public function test_user_cannot_rename_file_with_non_ascii_characters()
    {
        $response = $this
            ->actingAs($this->user)
            ->patch(route('files.update', $this->file), [
                'name' => 'FileWithÑonAscii.txt',
            ]);

        $response->assertSessionHasErrors([
            'name' => trans('validation.ascii', [
                'attribute' => 'name',
            ]),
        ], null, 'rename_file_'.$this->file->id);
    }

    public function test_user_cannot_rename_file_with_invalid_characters()
    {
        $validName = 'InvalidFileName.txt';

        $invalidChars = ['\\', '/', ':', '*', '?', '"', '<', '>', '|'];

        foreach ($invalidChars as $char) {
            $invalidName = str_replace('Name', $char.'Name', $validName);

            $response = $this
                ->actingAs($this->user)
                ->patch(route('files.update', $this->file), [
                    'name' => $invalidName,
                ]);

            $response->assertSessionHasErrors([
                'name' => trans('validation.regex', [
                    'attribute' => 'name',
                ]),
            ], null, 'rename_file_'.$this->file->id);
        }
    }

    public function test_user_cannot_rename_file_to_name_already_used_in_same_folder()
    {
        $otherFile = File::factory()->create([
            'folder_id' => $this->folder->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->patch(route('files.update', $otherFile), [
                'name' => $this->file->name,
            ]);

        $response->assertSessionHasErrors([
            'name' => trans('validation.unique', [
                'attribute' => 'name',
            ]),
        ], null, 'rename_file_'.$otherFile->id);
    }

    public function test_user_cannot_rename_file_to_name_already_used_by_folder_in_same_folder()
    {
        $otherFile = File::factory()->create([
            'folder_id' => $this->rootFolder->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->patch(route('files.update', $otherFile), [
                'name' => $this->folder->name,
            ]);

        $response->assertSessionHasErrors([
            'name' => trans('validation.unique_filename', [
                'name' => $this->folder->name,
            ]),
        ], null, 'rename_file_'.$otherFile->id);
    }

    public function test_user_can_only_rename_files_they_own()
    {
        $other = User::factory()->create();

        $response = $this
            ->actingAs($other)
            ->patch(route('files.update', $this->file), [
                'name' => 'Unauthorized Rename.txt',
            ]);

        $response->assertForbidden();
    }
}
