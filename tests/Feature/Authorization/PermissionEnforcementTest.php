<?php

use App\Livewire\Tables\DriversTable;
use App\Models\Driver;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);
});

test('a user with the User role can view drivers but not create them', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $this->actingAs($user);

    $this->get(route('drivers.index'))->assertOk();
    $this->get(route('drivers.create'))->assertForbidden();
});

test('a user with the User role cannot access user management', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $this->actingAs($user);

    $this->get(route('users.index'))->assertForbidden();
});

test('a user with the User role cannot access roles or permissions management', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $this->actingAs($user);

    $this->get(route('roles.index'))->assertForbidden();
    $this->get(route('permissions.index'))->assertForbidden();
});

test('a user with the User role cannot delete a driver through the Livewire action', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $this->actingAs($user);

    $driver = Driver::factory()->create();

    Livewire::test(DriversTable::class)
        ->call('deleteDriver', $driver->id)
        ->assertForbidden();

    expect($driver->fresh())->not->toBeNull();
});

test('a user with the User role cannot restore a driver through the Livewire action', function () {
    $user = User::factory()->create();
    $user->assignRole('User');
    $this->actingAs($user);

    $driver = Driver::factory()->create();
    $driver->delete();

    Livewire::test(DriversTable::class)
        ->call('restoreDriver', $driver->id)
        ->assertForbidden();

    expect($driver->fresh()->trashed())->toBeTrue();
});

test('a user with no role is forbidden everywhere', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('drivers.index'))->assertForbidden();
});

test('the Admin role can access every management page', function () {
    $user = User::factory()->create();
    $user->assignRole('Admin');
    $this->actingAs($user);

    $this->get(route('drivers.index'))->assertOk();
    $this->get(route('users.index'))->assertOk();
    $this->get(route('roles.index'))->assertOk();
    $this->get(route('permissions.index'))->assertOk();
});
