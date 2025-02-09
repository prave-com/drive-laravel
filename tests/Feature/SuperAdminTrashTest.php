<?php

// namespace Tests\Feature;

// use App\Enums\UserRole;
// use App\Models\File;
// use App\Models\Folder;
// use App\Models\User;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Tests\TestCase;

// class SuperAdminTrashTest extends TestCase
// {
//     use RefreshDatabase;

//     public function test_super_admin_can_restore_file()
//     {
//         $superAdmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
//         $file = File::factory()->trashed()->create();

//         $response = $this->actingAs($superAdmin)
//             ->post(route('trash.files.restore', ['file' => $file->id]));

//         $response->assertRedirect();
//         $this->assertDatabaseHas('files', [
//             'id' => $file->id,
//             'deleted_at' => null, // Ensure file is restored
//         ]);
//     }

//     public function test_super_admin_can_permanently_delete_file()
//     {
//         $superAdmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);

//         $file = File::factory()->create([
//             'full_path' => 'example-path/file.txt', // Tambahkan full_path
//             'deleted_at' => now(), // Simulasikan file dihapus
//         ]);

//         $response = $this->actingAs($superAdmin)
//             ->delete(route('trash.files.force-delete', ['file' => $file->id]));

//         $response->assertRedirect();
//         $this->assertDatabaseMissing('files', ['id' => $file->id]); // Pastikan file terhapus
//     }

//     public function test_super_admin_can_restore_folder()
//     {
//         $superAdmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);

//         $folder = Folder::factory()->create([
//             'full_path' => 'example-path/folder', // Tambahkan full_path
//             'deleted_at' => now(), // Simulasikan folder dihapus
//         ]);

//         $response = $this->actingAs($superAdmin)
//             ->post(route('trash.folders.restore', ['folder' => $folder->id]));

//         $response->assertRedirect();
//         $this->assertDatabaseHas('folders', [
//             'id' => $folder->id,
//             'deleted_at' => null, // Pastikan folder ter-restore
//         ]);
//     }

//     public function test_super_admin_can_permanently_delete_folder()
//     {
//         $superAdmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);

//         $folder = Folder::factory()->create([
//             'full_path' => 'example-path/folder', // Tambahkan full_path
//             'deleted_at' => now(), // Simulasikan folder dihapus
//         ]);

//         $response = $this->actingAs($superAdmin)
//             ->delete(route('trash.folders.force-delete', ['folder' => $folder->id]));

//         $response->assertRedirect();
//         $this->assertDatabaseMissing('folders', ['id' => $folder->id]); // Pastikan folder terhapus
//     }
// }
