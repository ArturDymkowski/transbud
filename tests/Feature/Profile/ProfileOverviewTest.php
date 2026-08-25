<?php

use App\Livewire\Profile\ProfileOverview;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('guest is redirected from the profile page', function () {
    $this->get(route('profile.edit'))->assertRedirect(route('login'));
});

test('profile page shows the authenticated user own data', function () {
    $user = actingAsAdmin();
    $user->update(['name' => 'Jan Kowalski', 'email' => 'jan.kowalski@example.com']);

    $this->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Jan Kowalski')
        ->assertSee('jan.kowalski@example.com')
        ->assertSee('Admin');
});

test('a user can update their own name and email', function () {
    $user = actingAsAdmin();

    Livewire::test(ProfileOverview::class)
        ->set('profileData.name', 'Nowe Imię')
        ->set('profileData.email', 'nowy@example.com')
        ->call('saveInfo')
        ->assertHasNoErrors();

    expect($user->refresh())
        ->name->toBe('Nowe Imię')
        ->email->toBe('nowy@example.com');
});

test('name and email are required and email must be unique', function () {
    $user = actingAsAdmin();
    User::factory()->create(['email' => 'taken@example.com']);

    Livewire::test(ProfileOverview::class)
        ->set('profileData.name', '')
        ->set('profileData.email', 'taken@example.com')
        ->call('saveInfo')
        ->assertHasErrors([
            'profileData.name' => 'required',
            'profileData.email' => 'unique',
        ]);

    expect($user->refresh()->email)->not->toBe('taken@example.com');
});

test('a user can change their own password by providing the current one', function () {
    $user = actingAsAdmin();
    $user->update(['password' => 'old-password']);

    Livewire::test(ProfileOverview::class)
        ->set('passwordData.current_password', 'old-password')
        ->set('passwordData.password', 'new-password')
        ->set('passwordData.password_confirmation', 'new-password')
        ->call('savePassword')
        ->assertHasNoErrors();

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('changing the password requires the correct current password', function () {
    $user = actingAsAdmin();
    $user->update(['password' => 'old-password']);

    Livewire::test(ProfileOverview::class)
        ->set('passwordData.current_password', 'wrong-password')
        ->set('passwordData.password', 'new-password')
        ->set('passwordData.password_confirmation', 'new-password')
        ->call('savePassword')
        ->assertHasErrors(['passwordData.current_password']);

    expect(Hash::check('old-password', $user->refresh()->password))->toBeTrue();
});

test('the new password must be confirmed', function () {
    actingAsAdmin()->update(['password' => 'old-password']);

    Livewire::test(ProfileOverview::class)
        ->set('passwordData.current_password', 'old-password')
        ->set('passwordData.password', 'new-password')
        ->set('passwordData.password_confirmation', 'does-not-match')
        ->call('savePassword')
        ->assertHasErrors(['passwordData.password' => 'confirmed']);
});
