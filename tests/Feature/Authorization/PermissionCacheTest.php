<?php

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Config::set('cache.default', 'database');
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);
});

test('revoking a permission from a role takes effect immediately, without manual cache clearing', function () {
    $role = Role::create(['name' => 'Dispatcher']);
    $role->givePermissionTo('drivers.view');

    $user = User::factory()->create();
    $user->assignRole($role);

    expect($user->can('drivers.view'))->toBeTrue();

    $role->revokePermissionTo('drivers.view');

    expect($user->fresh()->can('drivers.view'))->toBeFalse();
});

test('editing a role permission set via syncPermissions takes effect immediately', function () {
    $role = Role::create(['name' => 'Dispatcher']);
    $role->syncPermissions(Permission::where('name', 'drivers.view')->get());

    $user = User::factory()->create();
    $user->assignRole($role);

    expect($user->can('drivers.view'))->toBeTrue()
        ->and($user->can('vehicles.view'))->toBeFalse();

    $role->syncPermissions(Permission::where('name', 'vehicles.view')->get());

    $user = $user->fresh();
    expect($user->can('drivers.view'))->toBeFalse()
        ->and($user->can('vehicles.view'))->toBeTrue();
});

test('assigning a different role to a user takes effect immediately, without manual cache clearing', function () {
    $user = User::factory()->create();
    $user->assignRole('User');

    expect($user->can('drivers.view'))->toBeTrue()
        ->and($user->can('users.view'))->toBeFalse();

    $user->syncRoles(['Admin']);

    $user = $user->fresh();
    expect($user->can('users.view'))->toBeTrue();
});

test('deleting a permission clears the cache immediately', function () {
    $role = Role::create(['name' => 'Dispatcher']);
    $role->givePermissionTo('drivers.view');

    $user = User::factory()->create();
    $user->assignRole($role);

    expect($user->can('drivers.view'))->toBeTrue();

    Permission::where('name', 'drivers.view')->first()->delete();

    expect($user->fresh()->can('drivers.view'))->toBeFalse();
});
