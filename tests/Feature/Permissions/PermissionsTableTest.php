<?php

use App\Livewire\Tables\PermissionsTable;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

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
