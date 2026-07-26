<?php

use App\Livewire\Tables\UsersTable;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('guest is redirected from the users list', function () {
    auth()->logout();

    $this->get(route('users.index'))->assertRedirect(route('login'));
});

test('users index page lists users', function () {
    User::factory()->create(['name' => 'Jan Kowalski']);

    $this->get(route('users.index'))->assertOk()->assertSee('Jan Kowalski');
});

test('search filters users by name', function () {
    User::factory()->create(['name' => 'Jan Kowalski']);
    User::factory()->create(['name' => 'Anna Nowak']);

    Livewire::test(UsersTable::class)
        ->set('search', 'Kowalski')
        ->assertSee('Jan Kowalski')
        ->assertDontSee('Anna Nowak');
});

test('search filters users by email', function () {
    User::factory()->create(['name' => 'Jan Kowalski', 'email' => 'jan@example.com']);
    User::factory()->create(['name' => 'Anna Nowak', 'email' => 'anna@example.com']);

    Livewire::test(UsersTable::class)
        ->set('search', 'jan@example.com')
        ->assertSee('Jan Kowalski')
        ->assertDontSee('Anna Nowak');
});

test('active filter narrows the list to active or inactive users', function () {
    User::factory()->create(['name' => 'ActiveUser', 'is_active' => true]);
    User::factory()->create(['name' => 'InactiveUser', 'is_active' => false]);

    Livewire::test(UsersTable::class)
        ->set('isActive', '1')
        ->assertSee('ActiveUser')
        ->assertDontSee('InactiveUser');
});

test('trashed filter shows only soft deleted users', function () {
    User::factory()->create(['name' => 'KeepMe']);
    $deleted = User::factory()->create(['name' => 'DeletedUser']);
    $deleted->delete();

    Livewire::test(UsersTable::class)
        ->set('trashed', 'only')
        ->assertSee('DeletedUser')
        ->assertDontSee('KeepMe');
});

test('toggleActive flips the is_active flag', function () {
    $user = User::factory()->create(['is_active' => true]);

    Livewire::test(UsersTable::class)->call('toggleActive', $user->id);

    expect($user->refresh()->is_active)->toBeFalse();
});

test('deleteUser removes a single user', function () {
    $user = User::factory()->create();

    Livewire::test(UsersTable::class)->call('deleteUser', $user->id);

    $this->assertSoftDeleted($user);
});

test('deleteSelected soft deletes all selected users', function () {
    $users = User::factory()->count(3)->create();

    Livewire::test(UsersTable::class)
        ->set('selected', $users->pluck('id')->toArray())
        ->call('deleteSelected');

    $users->each(fn (User $user) => $this->assertSoftDeleted($user));
});
