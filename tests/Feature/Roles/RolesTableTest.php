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

test('search filters roles by name', function () {
    Role::create(['name' => 'Dispatcher']);
    Role::create(['name' => 'Accountant']);

    Livewire::test(RolesTable::class)
        ->set('search', 'Dispatch')
        ->assertSee('Dispatcher')
        ->assertDontSee('Accountant');
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

/**
 * The bulk selection UI (checkboxes + "delete selected" bar) is a second path to
 * deleteSelected(), which enforces roles.delete server-side — the UI needs the same
 * gate, or a view-only role sees a working-looking bulk-delete affordance that just
 * 403s when used.
 */
test('bulk selection is hidden without roles.delete and shown with it', function () {
    $role = Role::create(['name' => 'Dispatcher']);

    $limitedRole = Role::create(['name' => 'Limited']);
    $limitedRole->syncPermissions(Permission::where('name', 'roles.view')->get());
    $limited = User::factory()->create();
    $limited->assignRole($limitedRole);
    Livewire::actingAs($limited)->test(RolesTable::class)
        ->assertDontSee('name="selectAll"', false)
        ->assertDontSee('checkbox-'.$role->id, false)
        ->assertDontSee('wire:click="deleteSelected"', false);

    // Livewire::actingAs() overrides the acting user for subsequent Livewire::test()
    // calls too, so the "shows it" branch needs an explicit admin actor again —
    // it can't just fall back to beforeEach's actingAsAdmin().
    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    Livewire::actingAs($admin)->test(RolesTable::class)
        ->assertSee('name="selectAll"', false)
        ->assertSee('checkbox-'.$role->id, false)
        ->assertSee('wire:click="deleteSelected"', false);
});
