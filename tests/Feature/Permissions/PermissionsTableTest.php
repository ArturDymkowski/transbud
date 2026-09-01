<?php

use App\Livewire\Tables\PermissionsTable;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    actingAsAdmin();
});

test('guest is redirected from the permissions list', function () {
    auth()->logout();

    $this->get(route('permissions.index'))->assertRedirect(route('login'));
});

test('permissions index page lists seeded permissions', function () {
    $latestPermission = Permission::latest('id')->first();

    $this->get(route('permissions.index'))->assertOk()->assertSee($latestPermission->name);
});

test('deletePermission removes a permission', function () {
    $permission = Permission::where('name', 'roles.delete')->first();

    Livewire::test(PermissionsTable::class)->call('deletePermission', $permission->id);

    expect(Permission::find($permission->id))->toBeNull();
});

test('deleteSelected removes all selected permissions', function () {
    $permissions = Permission::whereIn('name', ['roles.delete', 'roles.edit'])->get();

    Livewire::test(PermissionsTable::class)
        ->set('selected', $permissions->pluck('id')->toArray())
        ->call('deleteSelected');

    $permissions->each(fn (Permission $permission) => expect(Permission::find($permission->id))->toBeNull());
});

test('search filters permissions by name', function () {
    Livewire::test(PermissionsTable::class)
        ->set('search', 'drivers.')
        ->assertSee('drivers.view')
        ->assertDontSee('vehicles.view');
});

test('sorting by roles_count orders permissions', function () {
    $adminOnlyPermission = Permission::where('name', 'roles.create')->first();
    $viewPermission = Permission::where('name', 'drivers.view')->first();

    Livewire::test(PermissionsTable::class)
        ->set('perPage', 100)
        ->call('sortBy', 'roles_count')
        ->assertSeeInOrder([$adminOnlyPermission->name, $viewPermission->name]);
});

/**
 * The bulk selection UI (checkboxes + "delete selected" bar) is a second path to
 * deleteSelected(), which enforces permissions.delete server-side — the UI needs the
 * same gate, or a view-only role sees a working-looking bulk-delete affordance that
 * just 403s when used.
 */
test('bulk selection is hidden without permissions.delete and shown with it', function () {
    $permission = Permission::where('name', 'roles.delete')->first();

    $limitedRole = Role::create(['name' => 'Limited']);
    $limitedRole->syncPermissions(Permission::where('name', 'permissions.view')->get());
    $limited = User::factory()->create();
    $limited->assignRole($limitedRole);
    Livewire::actingAs($limited)->test(PermissionsTable::class)
        ->assertDontSee('name="selectAll"', false)
        ->assertDontSee('checkbox-'.$permission->id, false)
        ->assertDontSee('wire:click="deleteSelected"', false);

    // Livewire::actingAs() overrides the acting user for subsequent Livewire::test()
    // calls too, so the "shows it" branch needs an explicit admin actor again —
    // it can't just fall back to beforeEach's actingAsAdmin().
    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    Livewire::actingAs($admin)->test(PermissionsTable::class)
        ->assertSee('name="selectAll"', false)
        ->assertSee('checkbox-'.$permission->id, false)
        ->assertSee('wire:click="deleteSelected"', false);
});
