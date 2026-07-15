<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'nama' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    $response->assertRedirect(
        route('dashboard', absolute: false)
    );

    $this->assertDatabaseHas('users', [
        'nama' => 'Test User',
        'email' => 'test@example.com',
        'role' => 'customer',
        'status_akun' => 'active',
    ]);
});
