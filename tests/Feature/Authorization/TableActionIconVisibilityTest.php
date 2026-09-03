<?php

use App\Livewire\Tables\ContractorAddressesTable;
use App\Livewire\Tables\ContractorsTable;
use App\Livewire\Tables\DeliveriesTable;
use App\Livewire\Tables\DriversTable;
use App\Livewire\Tables\GoodsTable;
use App\Livewire\Tables\UnitsTable;
use App\Livewire\Tables\UsersTable;
use App\Livewire\Tables\VehiclesTable;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Good;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

/**
 * The seeded "User" role is view-only everywhere (see RoleSeeder) — exactly the
 * case that used to leave dead edit/delete icons and clickable is_active toggles in
 * every table (they redirected to / threw a 403 instead of doing anything). Each
 * test below checks a User-role viewer doesn't see the edit link / delete button,
 * and gets a disabled toggle, while an Admin still gets fully working ones.
 */
beforeEach(function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);
});

/**
 * Checks the `disabled` HTML attribute specifically on the toggle input identified
 * by `wire:key="toggle-{id}"` — a plain assertSee('disabled') would also match
 * unrelated Tailwind `disabled:...` variant classes elsewhere on the page (e.g. the
 * pagination buttons), giving a false positive/negative.
 */
function assertToggleDisabled(Testable $component, int $id, bool $disabled): void
{
    preg_match('/wire:key="toggle-'.$id.'"(.*?)\/>/s', $component->html(), $matches);
    expect($matches)->not->toBeEmpty("Toggle {$id} not found in the rendered HTML.");

    expect(str_contains($matches[1], 'disabled'))->toBe($disabled,
        $disabled ? "Expected toggle {$id} to be disabled." : "Expected toggle {$id} to be enabled, found it disabled instead."
    );
}

test('contractors table hides edit/delete for a view-only role and shows them for Admin', function () {
    $contractor = Contractor::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    $component = Livewire::actingAs($user)->test(ContractorsTable::class)
        ->assertDontSee(route('contractors.edit', $contractor->id), false)
        ->assertDontSee('deleteContractor('.$contractor->id.')', false);
    assertToggleDisabled($component, $contractor->id, true);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    $component = Livewire::actingAs($admin)->test(ContractorsTable::class)
        ->assertSee(route('contractors.edit', $contractor->id), false)
        ->assertSee('deleteContractor('.$contractor->id.')', false);
    assertToggleDisabled($component, $contractor->id, false);
});

test('vehicles table hides edit/delete for a view-only role and shows them for Admin', function () {
    $vehicle = Vehicle::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    $component = Livewire::actingAs($user)->test(VehiclesTable::class)
        ->assertDontSee(route('vehicles.edit', $vehicle->id), false)
        ->assertDontSee('deleteVehicle('.$vehicle->id.')', false);
    assertToggleDisabled($component, $vehicle->id, true);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    $component = Livewire::actingAs($admin)->test(VehiclesTable::class)
        ->assertSee(route('vehicles.edit', $vehicle->id), false)
        ->assertSee('deleteVehicle('.$vehicle->id.')', false);
    assertToggleDisabled($component, $vehicle->id, false);
});

test('goods table hides edit/delete for a view-only role and shows them for Admin', function () {
    $good = Good::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    $component = Livewire::actingAs($user)->test(GoodsTable::class)
        ->assertDontSee(route('goods.edit', $good->id), false)
        ->assertDontSee('deleteGood('.$good->id.')', false);
    assertToggleDisabled($component, $good->id, true);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    $component = Livewire::actingAs($admin)->test(GoodsTable::class)
        ->assertSee(route('goods.edit', $good->id), false)
        ->assertSee('deleteGood('.$good->id.')', false);
    assertToggleDisabled($component, $good->id, false);
});

test('users table hides edit/delete for a view-only role and shows them for Admin', function () {
    $target = User::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    $component = Livewire::actingAs($user)->test(UsersTable::class)
        ->assertDontSee(route('users.edit', $target->id), false)
        ->assertDontSee('deleteUser('.$target->id.')', false);
    assertToggleDisabled($component, $target->id, true);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    $component = Livewire::actingAs($admin)->test(UsersTable::class)
        ->assertSee(route('users.edit', $target->id), false)
        ->assertSee('deleteUser('.$target->id.')', false);
    assertToggleDisabled($component, $target->id, false);
});

test('deliveries table hides edit/delete for a view-only role and shows them for Admin', function () {
    $delivery = Delivery::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    Livewire::actingAs($user)->test(DeliveriesTable::class)
        ->assertDontSee(route('deliveries.edit', $delivery->id), false)
        ->assertDontSee('deleteDelivery('.$delivery->id.')', false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    Livewire::actingAs($admin)->test(DeliveriesTable::class)
        ->assertSee(route('deliveries.edit', $delivery->id), false)
        ->assertSee('deleteDelivery('.$delivery->id.')', false);
});

test('contractor addresses table hides edit/delete for a view-only role and shows them for Admin', function () {
    $address = ContractorAddress::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    $component = Livewire::actingAs($user)->test(ContractorAddressesTable::class)
        ->assertDontSee(route('contractor-addresses.edit', $address->id), false)
        ->assertDontSee('deleteAddress('.$address->id.')', false);
    assertToggleDisabled($component, $address->id, true);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    $component = Livewire::actingAs($admin)->test(ContractorAddressesTable::class)
        ->assertSee(route('contractor-addresses.edit', $address->id), false)
        ->assertSee('deleteAddress('.$address->id.')', false);
    assertToggleDisabled($component, $address->id, false);
});

test('drivers table hides edit/delete for a view-only role and shows them for Admin', function () {
    $driver = Driver::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    $component = Livewire::actingAs($user)->test(DriversTable::class)
        ->assertDontSee(route('drivers.edit', $driver->id), false)
        ->assertDontSee('deleteDriver('.$driver->id.')', false);
    assertToggleDisabled($component, $driver->id, true);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    $component = Livewire::actingAs($admin)->test(DriversTable::class)
        ->assertSee(route('drivers.edit', $driver->id), false)
        ->assertSee('deleteDriver('.$driver->id.')', false);
    assertToggleDisabled($component, $driver->id, false);
});

test('drivers table embedded in a vehicle only shows the unlink icon with vehicles.edit', function () {
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();
    $vehicle->drivers()->attach($driver);

    // "User" role has vehicles.view but not vehicles.edit — sees the driver, not the unlink icon.
    $user = User::factory()->create();
    $user->assignRole('User');
    Livewire::actingAs($user)->test(DriversTable::class, ['vehicle' => $vehicle])
        ->assertDontSee('deleteDriver('.$driver->id.')', false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    Livewire::actingAs($admin)->test(DriversTable::class, ['vehicle' => $vehicle])
        ->assertSee('deleteDriver('.$driver->id.')', false);
});

test('units table hides edit/delete for a view-only role and shows them for Admin', function () {
    $unit = Unit::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    $component = Livewire::actingAs($user)->test(UnitsTable::class)
        ->assertDontSee(route('units.edit', $unit->id), false)
        ->assertDontSee('deleteUnit('.$unit->id.')', false);
    assertToggleDisabled($component, $unit->id, true);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    $component = Livewire::actingAs($admin)->test(UnitsTable::class)
        ->assertSee(route('units.edit', $unit->id), false)
        ->assertSee('deleteUnit('.$unit->id.')', false);
    assertToggleDisabled($component, $unit->id, false);
});

test('units table embedded in a good only shows the unlink icon with goods.edit', function () {
    $good = Good::factory()->create();
    $unit = Unit::factory()->create();
    $good->units()->attach($unit);

    // "User" role has goods.view but not goods.edit — sees the unit, not the unlink icon.
    $user = User::factory()->create();
    $user->assignRole('User');
    Livewire::actingAs($user)->test(UnitsTable::class, ['good' => $good])
        ->assertDontSee('deleteUnit('.$unit->id.')', false);

    $roleWithGoodsEdit = Role::create(['name' => 'Goods Editor']);
    $roleWithGoodsEdit->givePermissionTo(['units.view', 'goods.view', 'goods.edit']);
    $editor = User::factory()->create();
    $editor->assignRole($roleWithGoodsEdit);
    Livewire::actingAs($editor)->test(UnitsTable::class, ['good' => $good])
        ->assertSee('deleteUnit('.$unit->id.')', false);
});

/**
 * The "Przywróć" (restore) icon replaces edit/delete on a soft-deleted row and is
 * gated on the same x.edit permission used by restoreX() in each table component
 * (see ISSUES.md I2 / RISKS.md R9) — a view-only role must not see it either.
 */
test('contractors table hides restore for a view-only role and shows it for Admin', function () {
    $contractor = Contractor::factory()->create();
    $contractor->delete();

    $user = User::factory()->create();
    $user->assignRole('User');
    Livewire::actingAs($user)->test(ContractorsTable::class)
        ->set('trashed', 'only')
        ->assertDontSee('restoreContractor('.$contractor->id.')', false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    Livewire::actingAs($admin)->test(ContractorsTable::class)
        ->set('trashed', 'only')
        ->assertSee('restoreContractor('.$contractor->id.')', false);
});

test('vehicles table hides restore for a view-only role and shows it for Admin', function () {
    $vehicle = Vehicle::factory()->create();
    $vehicle->delete();

    $user = User::factory()->create();
    $user->assignRole('User');
    Livewire::actingAs($user)->test(VehiclesTable::class)
        ->set('trashed', 'only')
        ->assertDontSee('restoreVehicle('.$vehicle->id.')', false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    Livewire::actingAs($admin)->test(VehiclesTable::class)
        ->set('trashed', 'only')
        ->assertSee('restoreVehicle('.$vehicle->id.')', false);
});

test('goods table hides restore for a view-only role and shows it for Admin', function () {
    $good = Good::factory()->create();
    $good->delete();

    $user = User::factory()->create();
    $user->assignRole('User');
    Livewire::actingAs($user)->test(GoodsTable::class)
        ->set('trashed', 'only')
        ->assertDontSee('restoreGood('.$good->id.')', false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    Livewire::actingAs($admin)->test(GoodsTable::class)
        ->set('trashed', 'only')
        ->assertSee('restoreGood('.$good->id.')', false);
});

test('users table hides restore for a view-only role and shows it for Admin', function () {
    $target = User::factory()->create();
    $target->delete();

    $user = User::factory()->create();
    $user->assignRole('User');
    Livewire::actingAs($user)->test(UsersTable::class)
        ->set('trashed', 'only')
        ->assertDontSee('restoreUser('.$target->id.')', false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    Livewire::actingAs($admin)->test(UsersTable::class)
        ->set('trashed', 'only')
        ->assertSee('restoreUser('.$target->id.')', false);
});

test('contractor addresses table hides restore for a view-only role and shows it for Admin', function () {
    $address = ContractorAddress::factory()->create();
    $address->delete();

    $user = User::factory()->create();
    $user->assignRole('User');
    Livewire::actingAs($user)->test(ContractorAddressesTable::class)
        ->set('trashed', 'only')
        ->assertDontSee('restoreAddress('.$address->id.')', false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    Livewire::actingAs($admin)->test(ContractorAddressesTable::class)
        ->set('trashed', 'only')
        ->assertSee('restoreAddress('.$address->id.')', false);
});

test('drivers table hides restore for a view-only role and shows it for Admin', function () {
    $driver = Driver::factory()->create();
    $driver->delete();

    $user = User::factory()->create();
    $user->assignRole('User');
    Livewire::actingAs($user)->test(DriversTable::class)
        ->set('trashed', 'only')
        ->assertDontSee('restoreDriver('.$driver->id.')', false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    Livewire::actingAs($admin)->test(DriversTable::class)
        ->set('trashed', 'only')
        ->assertSee('restoreDriver('.$driver->id.')', false);
});

test('units table hides restore for a view-only role and shows it for Admin', function () {
    $unit = Unit::factory()->create();
    $unit->delete();

    $user = User::factory()->create();
    $user->assignRole('User');
    Livewire::actingAs($user)->test(UnitsTable::class)
        ->set('trashed', 'only')
        ->assertDontSee('restoreUnit('.$unit->id.')', false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    Livewire::actingAs($admin)->test(UnitsTable::class)
        ->set('trashed', 'only')
        ->assertSee('restoreUnit('.$unit->id.')', false);
});

/**
 * Bulk selection (header "select all" checkbox, per-row checkbox, and the
 * "delete selected" bar) is a second path to the same deleteSelected() action as
 * the row delete icon — it needs the identical permission gate, otherwise a
 * view-only role sees a working-looking bulk-delete UI that just 403s when used.
 */
function assertBulkSelectionVisible(Testable $component, int $rowId, bool $visible): void
{
    $assertion = $visible ? 'assertSee' : 'assertDontSee';
    $component->{$assertion}('name="selectAll"', false)
        ->{$assertion}('checkbox-'.$rowId, false)
        ->{$assertion}('wire:click="deleteSelected"', false);
}

test('contractors table hides bulk selection for a view-only role and shows it for Admin', function () {
    $contractor = Contractor::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    assertBulkSelectionVisible(Livewire::actingAs($user)->test(ContractorsTable::class), $contractor->id, false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    assertBulkSelectionVisible(Livewire::actingAs($admin)->test(ContractorsTable::class), $contractor->id, true);
});

test('vehicles table hides bulk selection for a view-only role and shows it for Admin', function () {
    $vehicle = Vehicle::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    assertBulkSelectionVisible(Livewire::actingAs($user)->test(VehiclesTable::class), $vehicle->id, false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    assertBulkSelectionVisible(Livewire::actingAs($admin)->test(VehiclesTable::class), $vehicle->id, true);
});

test('goods table hides bulk selection for a view-only role and shows it for Admin', function () {
    $good = Good::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    assertBulkSelectionVisible(Livewire::actingAs($user)->test(GoodsTable::class), $good->id, false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    assertBulkSelectionVisible(Livewire::actingAs($admin)->test(GoodsTable::class), $good->id, true);
});

test('users table hides bulk selection for a view-only role and shows it for Admin', function () {
    $target = User::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    assertBulkSelectionVisible(Livewire::actingAs($user)->test(UsersTable::class), $target->id, false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    assertBulkSelectionVisible(Livewire::actingAs($admin)->test(UsersTable::class), $target->id, true);
});

test('deliveries table hides bulk selection for a view-only role and shows it for Admin', function () {
    $delivery = Delivery::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    assertBulkSelectionVisible(Livewire::actingAs($user)->test(DeliveriesTable::class), $delivery->id, false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    assertBulkSelectionVisible(Livewire::actingAs($admin)->test(DeliveriesTable::class), $delivery->id, true);
});

test('contractor addresses table hides bulk selection for a view-only role and shows it for Admin', function () {
    $address = ContractorAddress::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    assertBulkSelectionVisible(Livewire::actingAs($user)->test(ContractorAddressesTable::class), $address->id, false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    assertBulkSelectionVisible(Livewire::actingAs($admin)->test(ContractorAddressesTable::class), $address->id, true);
});

test('drivers table hides bulk selection for a view-only role and shows it for Admin', function () {
    $driver = Driver::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    assertBulkSelectionVisible(Livewire::actingAs($user)->test(DriversTable::class), $driver->id, false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    assertBulkSelectionVisible(Livewire::actingAs($admin)->test(DriversTable::class), $driver->id, true);
});

test('drivers table embedded in a vehicle gates bulk selection on vehicles.edit', function () {
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();
    $vehicle->drivers()->attach($driver);

    $user = User::factory()->create();
    $user->assignRole('User');
    assertBulkSelectionVisible(
        Livewire::actingAs($user)->test(DriversTable::class, ['vehicle' => $vehicle]), $driver->id, false
    );

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    assertBulkSelectionVisible(
        Livewire::actingAs($admin)->test(DriversTable::class, ['vehicle' => $vehicle]), $driver->id, true
    );
});

test('units table hides bulk selection for a view-only role and shows it for Admin', function () {
    $unit = Unit::factory()->create();

    $user = User::factory()->create();
    $user->assignRole('User');
    assertBulkSelectionVisible(Livewire::actingAs($user)->test(UnitsTable::class), $unit->id, false);

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    assertBulkSelectionVisible(Livewire::actingAs($admin)->test(UnitsTable::class), $unit->id, true);
});

test('units table embedded in a good gates bulk selection on goods.edit', function () {
    $good = Good::factory()->create();
    $unit = Unit::factory()->create();
    $good->units()->attach($unit);

    $user = User::factory()->create();
    $user->assignRole('User');
    assertBulkSelectionVisible(
        Livewire::actingAs($user)->test(UnitsTable::class, ['good' => $good]), $unit->id, false
    );

    $roleWithGoodsEdit = Role::create(['name' => 'Goods Editor']);
    $roleWithGoodsEdit->givePermissionTo(['units.view', 'goods.view', 'goods.edit']);
    $editor = User::factory()->create();
    $editor->assignRole($roleWithGoodsEdit);
    assertBulkSelectionVisible(
        Livewire::actingAs($editor)->test(UnitsTable::class, ['good' => $good]), $unit->id, true
    );
});
