<?php

use App\Models\User;

test('confirm password route redirects to dashboard', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/confirm-password');

    $response->assertRedirect(
        route('dashboard')
    );
});

test('password can be confirmed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/confirm-password', [
        'password' => 'password',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test('password is not confirmed with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/confirm-password', [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();
});
