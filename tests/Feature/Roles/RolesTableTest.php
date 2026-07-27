<?php

use App\Livewire\Tables\RolesTable;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    actingAsAdmin();
});

test('guest is redirected from the roles list', function () {
    auth()->logout();

    $this->get(route('roles.index'))->assertRedirect(route('login'));
});

test('roles index page lists seeded roles', function () {
    $this->get(route('roles.index'))->assertOk()->assertSee('Admin')->assertSee('User');
});

test('deleteRole removes a role that has no users assigned', function () {
    $role = Role::create(['name' => 'Dispatcher']);

    Livewire::test(RolesTable::class)->call('deleteRole', $role->id);

    expect(Role::find($role->id))->toBeNull();
});

test('deleteRole refuses to delete a role assigned to a user', function () {
    $role = Role::create(['name' => 'Dispatcher']);
    User::factory()->create()->assignRole($role);

    Livewire::test(RolesTable::class)->call('deleteRole', $role->id);

    expect(Role::find($role->id))->not->toBeNull();
});

test('a user without the roles.delete permission cannot delete a role', function () {
    $limitedRole = Role::create(['name' => 'Limited']);
    $limitedRole->syncPermissions(Permission::where('name', 'roles.view')->get());

    $user = User::factory()->create();
    $user->assignRole($limitedRole);
    $this->actingAs($user);

    $role = Role::create(['name' => 'Dispatcher']);

    Livewire::test(RolesTable::class)->call('deleteRole', $role->id)
        ->assertForbidden();

    expect(Role::find($role->id))->not->toBeNull();
});
