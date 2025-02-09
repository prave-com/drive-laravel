<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertOk();
});

test('new users can register with valid data', function () {
    $response = $this->post('/register', [
        'email' => 'test@example.com',
        'name' => 'Test User',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registration fails with email longer than 255 characters', function () {
    $response = $this->post('/register', [
        'email' => str_repeat('a', 244).'@example.com',
        'name' => 'Test User',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'email' => trans('validation.max.string', [
            'attribute' => 'email',
            'max' => '255',
        ]),
    ]);
});

test('registration fails with name longer than 32 characters', function () {
    $response = $this->post('/register', [
        'email' => 'test@example.com',
        'name' => str_repeat('a', 33),
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'name' => trans('validation.max.string', [
            'attribute' => 'name',
            'max' => '32',
        ]),
    ]);
});
