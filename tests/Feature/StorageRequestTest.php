<?php

namespace Tests\Feature;

use App\Models\StorageRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorageRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    // public function test_user_can_view_storage_requests_page()
    // {
    //     $response = $this
    //         ->actingAs($this->user)
    //         ->get(route('storage-requests.index'));

    //     $response->assertOk();
    //     $response->assertViewIs('storage_requests.index');
    // }

    // public function test_user_can_access_create_storage_request_page()
    // {
    //     $response = $this
    //         ->actingAs($this->user)
    //         ->get(route('storage-requests.create'));

    //     $response->assertOk();
    //     $response->assertViewIs('storage_requests.create');
    // }

    /**
     * Test successful submission with 5GB option.
     */
    public function test_submit_storage_request_with_5gb()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('storage-requests.store'), [
                'request_quota' => 5,
                'reason' => 'Need more space for documents',
            ]);

        $response->assertRedirect(route('storage-requests.index'));

        $this->assertDatabaseHas('storage_requests', [
            'request_quota' => 5,
            'reason' => 'Need more space for documents',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_submit_storage_request_with_10gb()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('storage-requests.store'), [
                'request_quota' => 10,
                'reason' => 'Storing large media files',
            ]);

        $response->assertRedirect(route('storage-requests.index'));

        $this->assertDatabaseHas('storage_requests', [
            'request_quota' => 10,
            'reason' => 'Storing large media files',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test successful submission with a custom quota.
     */
    public function test_submit_storage_request_with_custom_quota()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('storage-requests.store'), [
                'custom_quota' => 15,
                'reason' => 'Custom quota required for project files',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('storage_requests', [
            'request_quota' => 15,
            'reason' => 'Custom quota required for project files',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test submission fails when reason is missing.
     */
    public function test_submit_storage_request_fails_without_reason()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('storage-requests.store'), [
                'request_quota' => 5,
            ]);

        $response->assertSessionHasErrors(['reason']);

        $this->assertDatabaseMissing('storage_requests', [
            'request_quota' => 5 * 1073741824,
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test submission fails when no quota is selected.
     */
    public function test_submit_storage_request_fails_without_quota()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('storage-requests.store'), [
                'reason' => 'Forgot to select quota',
            ]);

        $response->assertSessionHasErrors(['request_quota']);

        $this->assertDatabaseMissing('storage_requests', [
            'reason' => 'Forgot to select quota',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_it_cannot_submit_a_storage_request_without_quota_and_reason()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('storage-requests.store'), [
                'reason' => '',
            ]);

        $response->assertSessionHasErrors(['request_quota', 'reason']);

        $this->assertCount(0, StorageRequest::all());
    }

    /**
     * Test submission fails when custom quota is negative.
     */
    public function test_submit_storage_request_fails_with_negative_quota()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('storage-requests.store'), [
                'custom_quota' => -5,
                'reason' => 'Invalid custom quota',
            ]);

        $response->assertSessionHasErrors(['custom_quota']);

        $this->assertDatabaseMissing('storage_requests', [
            'request_quota' => -5,
            'reason' => 'Invalid custom quota',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_submit_storage_request_with_more_than_50gb_fail()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('storage-requests.store'), [
                'custom_quota' => 51,
                'reason' => 'Storing large media files',
            ]);

        $response->assertSessionHasErrors(['custom_quota']);
    }

    public function test_user_cannot_submit_storage_request_with_invalid_quota()
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('storage-requests.store'), [
                'request_quota' => 20,
                'reason' => 'Need more storage.',
            ]);

        $response->assertSessionHasErrors('request_quota');
    }

    // public function test_user_can_only_see_their_own_storage_requests()
    // {
    //     $otherUser = User::factory()->create();
    //     StorageRequest::factory()->create(['user_id' => $otherUser->id]);
    //     StorageRequest::factory()->create(['user_id' => $this->user->id]);

    //     $response = $this
    //         ->actingAs($this->user)
    //         ->get(route('storage-requests.index'));

    //     $response->assertSee($this->user->storageRequests->first()->reason);
    //     $response->assertDontSee($otherUser->storageRequests->first()->reason);
    // }
}
