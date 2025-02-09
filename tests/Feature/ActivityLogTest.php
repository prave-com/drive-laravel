<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_activity_logs()
    {
        $admin = User::factory()->admin()->create();

        Activity::create([
            'description' => 'Admin log test',
            'causer_id' => $admin->id,
            // 'causer_type' => User::class,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('activity-logs.index'));

        // $response->assertOk();
        // $response->assertViewIs('admin.activity-logs.index');
        $response->assertSee('Admin log test');
    }

    public function test_user_cannot_view_activity_logs()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('activity-logs.index'));

        $response->assertForbidden();
    }
}
