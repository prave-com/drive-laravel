<?php

namespace Tests\Feature;

use App\Enums\StorageRequestStatus;
use App\Models\StorageRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStorageRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->create();

        $this->user->storage()->create([
            'total_quota' => 3221225472,
        ]);
    }

    public function test_admin_can_approve_5gb_storage_request()
    {
        $request = StorageRequest::create([
            'request_quota' => 5,
            'reason' => 'Need more storage',
            'user_id' => $this->user->id,
        ]);

        $this
            ->actingAs($this->admin)
            ->put(route('storage-requests.update', $request), [
                'status' => StorageRequestStatus::APPROVED->value,
            ])
            ->assertRedirect(route('storage-requests.index'));

        $this->assertDatabaseHas('storage_requests', [
            'id' => $request->id,
            'status' => StorageRequestStatus::APPROVED->value,
        ]);

        $this->assertDatabaseHas('storages', [
            'user_id' => $this->user->id,
            'total_quota' => 3221225472 + (5 * 1073741824),
        ]);
    }

    public function test_admin_can_approve_10gb_storage_request()
    {
        $request = StorageRequest::create([
            'request_quota' => 10,
            'reason' => 'Need more storage',
            'user_id' => $this->user->id,
        ]);

        $this
            ->actingAs($this->admin)
            ->put(route('storage-requests.update', $request), [
                'status' => StorageRequestStatus::APPROVED->value,
            ])
            ->assertRedirect(route('storage-requests.index'));

        $this->assertDatabaseHas('storage_requests', [
            'id' => $request->id,
            'status' => StorageRequestStatus::APPROVED->value,
        ]);

        $this->assertDatabaseHas('storages', [
            'user_id' => $this->user->id,
            'total_quota' => 3221225472 + (10 * 1073741824),
        ]);
    }

    public function test_admin_can_approve_50gb_storage_request()
    {
        $request = StorageRequest::create([
            'request_quota' => 50,
            'reason' => 'Need more storage',
            'user_id' => $this->user->id,
        ]);

        $this
            ->actingAs($this->admin)
            ->put(route('storage-requests.update', $request), [
                'status' => StorageRequestStatus::APPROVED->value,
            ])
            ->assertRedirect(route('storage-requests.index'));

        $this->assertDatabaseHas('storage_requests', [
            'id' => $request->id,
            'status' => StorageRequestStatus::APPROVED->value,
        ]);

        $this->assertDatabaseHas('storages', [
            'user_id' => $this->user->id,
            'total_quota' => 3221225472 + (50 * 1073741824),
        ]);
    }

    public function test_admin_can_reject_5gb_storage_request()
    {
        $storageRequest = StorageRequest::factory()->create([
            'request_quota' => 5,
            'reason' => 'Permintaan tidak valid',
            'user_id' => $this->user->id,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->put(route('storage-requests.update', $storageRequest), [
                'status' => StorageRequestStatus::REJECTED->value,
            ]);

        $response->assertRedirect(route('storage-requests.index'));

        $this->assertDatabaseHas('storage_requests', [
            'id' => $storageRequest->id,
            'status' => StorageRequestStatus::REJECTED,
        ]);
    }

    public function test_admin_can_reject_10gb_storage_request()
    {
        $storageRequest = StorageRequest::factory()->create([
            'request_quota' => 10,
            'reason' => 'Tidak ada alasan yang cukup jelas',
            'user_id' => $this->user->id,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->put(route('storage-requests.update', $storageRequest), [
                'status' => StorageRequestStatus::REJECTED->value,
            ]);

        $response->assertRedirect(route('storage-requests.index'));

        $this->assertDatabaseHas('storage_requests', [
            'id' => $storageRequest->id,
            'status' => StorageRequestStatus::REJECTED,
        ]);
    }

    public function test_admin_can_reject_51gb_storage_request()
    {
        $storageRequest = StorageRequest::factory()->create([
            'request_quota' => 51,
            'reason' => 'Kuota terlalu besar untuk kebutuhan biasa',
            'user_id' => $this->user->id,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->put(route('storage-requests.update', $storageRequest), [
                'status' => StorageRequestStatus::REJECTED->value,
            ]);

        $response->assertRedirect(route('storage-requests.index'));
        $this->assertDatabaseHas('storage_requests', [
            'id' => $storageRequest->id,
            'status' => StorageRequestStatus::REJECTED,
        ]);
    }

    public function test_user_cannot_approve_or_reject_storage_request()
    {
        $storageRequest = StorageRequest::factory()->create([
            'request_quota' => 5,
            'reason' => 'Testing',
            'user_id' => $this->user->id,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->put(route('storage-requests.update', $storageRequest), [
                'status' => StorageRequestStatus::APPROVED->value,
            ]);

        $response->assertForbidden();
    }

    public function test_storage_request_requires_reason()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('storage-requests.store'), [
                'request_quota' => 5,
            ]);

        $response->assertSessionHasErrors(['reason']);
    }

    public function test_storage_request_requires_quota()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('storage-requests.store'), [
                'reason' => 'Butuh penyimpanan tambahan',
            ]);

        $response->assertSessionHasErrors(['request_quota']);
    }

    public function test_storage_request_update_requires_valid_status()
    {
        $storageRequest = StorageRequest::factory()->create([
            'request_quota' => 5,
            'reason' => 'Testing',
            'user_id' => $this->user->id,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->put(route('storage-requests.update', $storageRequest), [
                'status' => 'invalid_status',
            ]);

        $response->assertSessionHasErrors(['status']);
    }

    public function test_storage_request_remains_pending_without_admin_action()
    {
        $storageRequest = StorageRequest::factory()->create([
            'request_quota' => 10,
            'reason' => 'Testing pending state',
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('storage_requests', [
            'id' => $storageRequest->id,
            'status' => StorageRequestStatus::PENDING,
        ]);
    }
}
