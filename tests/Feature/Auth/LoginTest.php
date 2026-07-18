<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('guest is redirected from the root to the login page', function () {
    $this->get('/')->assertRedirect(route('login'));
});

test('authenticated user is redirected from the root to the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/')
        ->assertRedirect(route('dashboard'));
});

test('login page can be rendered', function () {
    $this->get(route('login'))->assertOk();
});

test('user can log in with correct credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('correct-password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'correct-password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('user cannot log in with incorrect credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('correct-password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors(['email', 'password']);
    $this->assertGuest();
});

test('login is throttled after too many failed attempts', function () {
    $user = User::factory()->create([
        'password' => Hash::make('correct-password'),
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'correct-password',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
});

test('authenticated user can log out', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('login.destroy'));

    $response->assertRedirect('/');
    $this->assertGuest();
});
