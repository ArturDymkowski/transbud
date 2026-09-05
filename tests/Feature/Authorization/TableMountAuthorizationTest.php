<?php

use App\Livewire\Calendars\DeliveriesCalendar;
use App\Livewire\Planners\DeliveriesPlanner;
use App\Livewire\Tables\ContractorAddressesTable;
use App\Livewire\Tables\ContractorsTable;
use App\Livewire\Tables\DeliveriesTable;
use App\Livewire\Tables\DriversTable;
use App\Livewire\Tables\GoodsTable;
use App\Livewire\Tables\PermissionsTable;
use App\Livewire\Tables\RolesTable;
use App\Livewire\Tables\UnitsTable;
use App\Livewire\Tables\UsersTable;
use App\Livewire\Tables\VehiclesTable;
use App\Models\Contractor;
use App\Models\Good;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);
});

function guardedListingComponents(): array
{
    return [
        'ContractorsTable' => [ContractorsTable::class, 'contractors.view'],
        'DeliveriesTable' => [DeliveriesTable::class, 'deliveries.view'],
        'GoodsTable' => [GoodsTable::class, 'goods.view'],
        'PermissionsTable' => [PermissionsTable::class, 'permissions.view'],
        'RolesTable' => [RolesTable::class, 'roles.view'],
        'UsersTable' => [UsersTable::class, 'users.view'],
        'VehiclesTable' => [VehiclesTable::class, 'vehicles.view'],
        'DriversTable (standalone)' => [DriversTable::class, 'drivers.view'],
        'UnitsTable (standalone)' => [UnitsTable::class, 'units.view'],
        'ContractorAddressesTable (standalone)' => [ContractorAddressesTable::class, 'contractor-addresses.view'],
        'DeliveriesCalendar' => [DeliveriesCalendar::class, 'deliveries.view'],
        'DeliveriesPlanner' => [DeliveriesPlanner::class, 'deliveries.view'],
    ];
}

test('mounting a listing component directly (bypassing route middleware) is forbidden without its view permission', function (string $componentClass) {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test($componentClass)->assertForbidden();
})->with(guardedListingComponents());

test('mounting a listing component directly (bypassing route middleware) succeeds with only its view permission', function (string $componentClass, string $permission) {
    $role = Role::create(['name' => "view-only-{$permission}"]);
    $role->syncPermissions(Permission::where('name', $permission)->get());

    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    Livewire::test($componentClass)->assertOk();
})->with(guardedListingComponents());

test('DriversTable embedded in a vehicle authorizes against vehicles.view, not drivers.view', function () {
    $vehicle = Vehicle::factory()->create();

    $role = Role::create(['name' => 'vehicles-viewer']);
    $role->syncPermissions(Permission::where('name', 'vehicles.view')->get());
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    Livewire::test(DriversTable::class, ['vehicle' => $vehicle])->assertOk();
});

test('UnitsTable embedded in a good authorizes against goods.view, not units.view', function () {
    $good = Good::factory()->create();

    $role = Role::create(['name' => 'goods-viewer']);
    $role->syncPermissions(Permission::where('name', 'goods.view')->get());
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    Livewire::test(UnitsTable::class, ['good' => $good])->assertOk();
});

test('ContractorAddressesTable embedded in a contractor authorizes against contractors.view, not contractor-addresses.view', function () {
    $contractor = Contractor::factory()->create();

    $role = Role::create(['name' => 'contractors-viewer']);
    $role->syncPermissions(Permission::where('name', 'contractors.view')->get());
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    Livewire::test(ContractorAddressesTable::class, ['contractor' => $contractor])->assertOk();
});
