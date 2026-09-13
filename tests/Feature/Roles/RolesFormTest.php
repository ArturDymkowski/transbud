<?php

use App\Livewire\Forms\RolesForm;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    actingAsAdmin();
});

test('role name is required', function () {
    Livewire::test(RolesForm::class)
        ->set('roleData.name', '')
        ->call('save')
        ->assertHasErrors(['roleData.name' => 'required']);
});

test('role name must be unique', function () {
    Role::create(['name' => 'Dispatcher']);

    Livewire::test(RolesForm::class)
        ->set('roleData.name', 'Dispatcher')
        ->call('save')
        ->assertHasErrors(['roleData.name' => 'unique']);
});

test('a new role can be created with selected permissions', function () {
    $permission = Permission::where('name', 'drivers.view')->first();

    Livewire::test(RolesForm::class)
        ->set('roleData.name', 'Dispatcher')
        ->set('selectedPermissions', [(string) $permission->id])
        ->call('save')
        ->assertRedirect(route('roles.index'));

    $role = Role::where('name', 'Dispatcher')->first();

    expect($role)->not->toBeNull();
    expect($role->hasPermissionTo('drivers.view'))->toBeTrue();
    expect($role->hasPermissionTo('drivers.delete'))->toBeFalse();
});

test('saving with every permission selected assigns them all to the role', function () {
    // The "select all" toggle itself is client-side only (Alpine.js checks every
    // checkbox in the DOM); what actually reaches the server is a fully populated
    // selectedPermissions array, which is what this simulates.
    $allPermissionIds = Permission::pluck('id')->map(fn ($id) => (string) $id)->all();

    Livewire::test(RolesForm::class)
        ->set('roleData.name', 'Dispatcher')
        ->set('selectedPermissions', $allPermissionIds)
        ->call('save')
        ->assertRedirect(route('roles.index'));

    $role = Role::where('name', 'Dispatcher')->first();

    expect($role->permissions()->count())->toBe(Permission::count());
});

test('editing a role updates its name and permission set', function () {
    $role = Role::create(['name' => 'Dispatcher']);
    $role->syncPermissions(Permission::where('name', 'drivers.view')->get());

    $vehiclesView = Permission::where('name', 'vehicles.view')->first();

    Livewire::test(RolesForm::class, ['role' => $role])
        ->set('roleData.name', 'Senior Dispatcher')
        ->set('selectedPermissions', [(string) $vehiclesView->id])
        ->call('save')
        ->assertRedirect(route('roles.index'));

    $role->refresh();

    expect($role->name)->toBe('Senior Dispatcher');
    expect($role->hasPermissionTo('vehicles.view'))->toBeTrue();
    expect($role->hasPermissionTo('drivers.view'))->toBeFalse();
});

test('editing a role keeps its own name valid despite the uniqueness rule', function () {
    $role = Role::create(['name' => 'Dispatcher']);

    Livewire::test(RolesForm::class, ['role' => $role])
        ->set('roleData.name', 'Dispatcher')
        ->call('save')
        ->assertHasNoErrors(['roleData.name'])
        ->assertRedirect(route('roles.index'));
});

test('a plain Admin can open the Admin role, but only as a read-only view', function () {
    $adminRole = Role::where('name', 'Admin')->firstOrFail();

    Livewire::test(RolesForm::class, ['role' => $adminRole])
        ->assertOk()
        ->assertSet('isReadOnly', true);
});

test('a plain Admin cannot submit changes to the Admin role', function () {
    $adminRole = Role::where('name', 'Admin')->firstOrFail();

    Livewire::test(RolesForm::class, ['role' => $adminRole])
        ->set('roleData.name', 'Admin')
        ->call('save')
        ->assertForbidden();
});

/**
 * Scoped to the actual <input> tag (matched by its id) rather than a plain
 * `toContain('disabled')` — the page also has Tailwind `disabled:...` variant
 * classes elsewhere, which would make a bare substring check pass regardless
 * of whether the field itself is really disabled.
 */
function roleNameInputTag(string $html): string
{
    preg_match('/<input[^>]*id="roleData\.name"[^>]*>/s', $html, $matches);

    return $matches[0] ?? '';
}

test('the Admin role form fields are disabled for a plain Admin', function () {
    $adminRole = Role::where('name', 'Admin')->firstOrFail();

    $html = Livewire::test(RolesForm::class, ['role' => $adminRole])->html();

    expect(roleNameInputTag($html))->toContain('disabled');
});

test('a Dispatcher role form is not read-only', function () {
    $role = Role::create(['name' => 'Dispatcher']);

    $html = Livewire::test(RolesForm::class, ['role' => $role])->html();

    expect(roleNameInputTag($html))->not->toContain('disabled');
});

test('a Super Admin can open and edit the Admin role', function () {
    $superAdmin = User::factory()->create(['is_super_admin' => true]);
    $superAdmin->assignRole('Admin');
    test()->actingAs($superAdmin);

    $adminRole = Role::where('name', 'Admin')->firstOrFail();
    $vehiclesView = Permission::where('name', 'vehicles.view')->first();

    Livewire::test(RolesForm::class, ['role' => $adminRole])
        ->set('selectedPermissions', [(string) $vehiclesView->id])
        ->call('save')
        ->assertRedirect(route('roles.index'));

    expect($adminRole->fresh()->hasPermissionTo('vehicles.view'))->toBeTrue();
});
