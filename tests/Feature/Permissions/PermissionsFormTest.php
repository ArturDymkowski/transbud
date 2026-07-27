<?php

use App\Livewire\Forms\PermissionsForm;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    actingAsAdmin();
});

test('permission name is required', function () {
    $permission = Permission::where('name', 'goods.view')->first();

    Livewire::test(PermissionsForm::class, ['permission' => $permission])
        ->set('permissionData.name', '')
        ->call('save')
        ->assertHasErrors(['permissionData.name' => 'required']);
});

test('permission name must be unique', function () {
    $permission = Permission::where('name', 'goods.view')->first();

    Livewire::test(PermissionsForm::class, ['permission' => $permission])
        ->set('permissionData.name', 'goods.edit')
        ->call('save')
        ->assertHasErrors(['permissionData.name' => 'unique']);
});

test('a permission can be renamed', function () {
    $permission = Permission::where('name', 'goods.view')->first();

    Livewire::test(PermissionsForm::class, ['permission' => $permission])
        ->set('permissionData.name', 'goods.list')
        ->call('save')
        ->assertRedirect(route('permissions.index'));

    expect($permission->refresh()->name)->toBe('goods.list');
});
