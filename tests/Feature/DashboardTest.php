<?php

test('redirects unauthenticated users to login when visiting dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
