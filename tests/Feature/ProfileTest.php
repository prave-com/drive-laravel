<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

// test('profile page is displayed', function () {
//     $user = User::factory()->create();

//     $response = $this
//         ->actingAs($user)
//         ->get('/profile');

//     $response->assertOk();
// });

test('profile information can be updated without email', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('update profile fails because prohibit email', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'email' => 'invalidemail@example.com',
            'name' => 'Test User',
        ]);

    $response->assertSessionHasErrors([
        'email' => trans('validation.prohibited', [
            'attribute' => 'email',
        ]),
    ]);
});

test('update profile fails with name longer than 32 characters', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => str_repeat('a', 33),
        ]);

    $response->assertSessionHasErrors([
        'name' => trans('validation.max.string', [
            'attribute' => 'name',
            'max' => '32',
        ]),
    ]);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});

test('user can upload an avatar', function () {
    Storage::fake('local');

    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch('/profile', [
        'name' => 'Test User',
        'avatar' => UploadedFile::fake()->image('avatar.jpg'),
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->fresh();

    $this->assertNotNull($user->avatar);

    Storage::disk('local')->assertExists("avatars/{$user->avatar}");
});

test('old avatar is deleted when a new one is uploaded', function () {
    Storage::fake('local');

    $user = User::factory()->withAvatar()->create();
    $oldAvatar = $user->avatar;

    Storage::disk('local')->put("avatars/{$oldAvatar}", 'old avatar content');

    $response = $this->actingAs($user)->patch('/profile', [
        'name' => 'Test User',
        'avatar' => UploadedFile::fake()->image('new_avatar.jpg'),
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    // Assert that the old avatar was deleted
    Storage::disk('local')->assertMissing("avatars/{$oldAvatar}");

    $user->fresh();

    // Assert that the new avatar was stored
    Storage::disk('local')->assertExists("avatars/{$user->avatar}");

    $this->assertNotNull($user->avatar);
});

test('update profile fails with invalid avatar', function () {
    Storage::fake('local');

    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch('/profile', [
        'name' => 'Test User',
        'avatar' => 'not-an-image.txt',
    ]);

    $response->assertSessionHasErrors([
        'avatar' => trans('validation.image', [
            'attribute' => 'avatar',
        ]),
        'avatar' => trans('validation.mimes', [
            'attribute' => 'avatar',
            'values' => 'jpg, png, jpeg, gif, svg',
        ]),
        'avatar' => trans('validation.extensions', [
            'attribute' => 'avatar',
            'values' => 'jpg, png, jpeg, gif, svg',
        ]),
    ]);
});

test('update profile fails with oversized avatar', function () {
    Storage::fake('local');

    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch('/profile', [
        'name' => 'Test User',
        'avatar' => UploadedFile::fake()->image('avatar.jpg')->size(3000),
    ]);

    $response->assertSessionHasErrors([
        'avatar' => trans('validation.max.file', [
            'attribute' => 'avatar',
            'max' => '2048',
        ]),
    ]);
});

test('avatar is deleted when user is pruned and has an avatar', function () {
    Storage::fake('local');

    $user = User::factory()->unverified()->withAvatar()->create([
        'created_at' => now()->subDays(2),
    ]);

    Storage::disk('local')->put("avatars/{$user->avatar}", 'avatar content');

    Artisan::call('model:prune', ['--model' => User::class]);

    Storage::disk('local')->assertMissing("avatars/{$user->avatar}");
});

test('user directory is deleted when the user is deleted', function () {
    Storage::fake('local');

    $user = User::factory()->create();

    $userId = $user->id;
    Storage::disk('local')->makeDirectory("drive/{$userId}");

    Storage::disk('local')->assertExists("drive/{$userId}");

    $user->delete();

    Storage::disk('local')->assertMissing("drive/{$userId}");
});
