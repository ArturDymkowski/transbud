<?php

use App\Livewire\Tables\VehicleDriversTable;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('it lists only drivers assigned to the given vehicle', function () {
    $vehicle = Vehicle::factory()->create();
    $otherVehicle = Vehicle::factory()->create();

    $assignedDriver = Driver::factory()->create(['name' => 'Jan Kowalski']);
    $otherDriver = Driver::factory()->create(['name' => 'Adam Nowak']);

    $vehicle->drivers()->attach($assignedDriver);
    $otherVehicle->drivers()->attach($otherDriver);

    Livewire::test(VehicleDriversTable::class, ['vehicle' => $vehicle])
        ->assertSeeHtml('assigned-driver-row-'.$assignedDriver->id)
        ->assertDontSeeHtml('assigned-driver-row-'.$otherDriver->id);
});

test('it shows the assignment date from the pivot record', function () {
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();

    $vehicle->drivers()->attach($driver, [
        'created_at' => '2026-01-15 10:00:00',
        'updated_at' => '2026-01-15 10:00:00',
    ]);

    Livewire::test(VehicleDriversTable::class, ['vehicle' => $vehicle])
        ->assertSee('2026-01-15');
});

test('search filters assigned drivers by name', function () {
    $vehicle = Vehicle::factory()->create();

    $vehicle->drivers()->attach(Driver::factory()->create(['name' => 'Jan Kowalski']));
    $vehicle->drivers()->attach(Driver::factory()->create(['name' => 'Adam Nowak']));

    Livewire::test(VehicleDriversTable::class, ['vehicle' => $vehicle])
        ->set('search', 'Kowalski')
        ->assertSee('Jan Kowalski')
        ->assertDontSee('Adam Nowak');
});

test('search does not match drivers assigned to other vehicles', function () {
    $vehicle = Vehicle::factory()->create();
    $otherVehicle = Vehicle::factory()->create();

    $matchingDriver = Driver::factory()->create(['name' => 'Jan Kowalski']);
    $otherVehicleDriver = Driver::factory()->create(['name' => 'Jan Nowicki']);

    $vehicle->drivers()->attach($matchingDriver);
    $otherVehicle->drivers()->attach($otherVehicleDriver);

    Livewire::test(VehicleDriversTable::class, ['vehicle' => $vehicle])
        ->set('search', 'Jan')
        ->assertSeeHtml('assigned-driver-row-'.$matchingDriver->id)
        ->assertDontSeeHtml('assigned-driver-row-'.$otherVehicleDriver->id);
});

test('removeAssignment detaches the driver from the vehicle without deleting the driver', function () {
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();

    $vehicle->drivers()->attach($driver);

    Livewire::test(VehicleDriversTable::class, ['vehicle' => $vehicle])
        ->call('removeAssignment', $driver->id)
        ->assertDontSeeHtml('assigned-driver-row-'.$driver->id);

    expect($vehicle->drivers()->where('drivers.id', $driver->id)->exists())->toBeFalse();
    expect(Driver::find($driver->id))->not->toBeNull();
});

test('assignable driver options exclude drivers already assigned to the vehicle', function () {
    $vehicle = Vehicle::factory()->create();
    $assigned = Driver::factory()->create(['name' => 'Jan Kowalski']);
    $unassigned = Driver::factory()->create(['name' => 'Adam Nowak']);

    $vehicle->drivers()->attach($assigned);

    $options = Livewire::test(VehicleDriversTable::class, ['vehicle' => $vehicle])
        ->get('assignableDriverOptions');

    expect($options)->toHaveKey($unassigned->id)
        ->and($options)->not->toHaveKey($assigned->id);
});

test('openAssignModal opens the modal', function () {
    $vehicle = Vehicle::factory()->create();

    Livewire::test(VehicleDriversTable::class, ['vehicle' => $vehicle])
        ->assertSet('showAssignModal', false)
        ->call('openAssignModal')
        ->assertSet('showAssignModal', true);
});

test('assignDriver requires a driver to be selected', function () {
    $vehicle = Vehicle::factory()->create();

    Livewire::test(VehicleDriversTable::class, ['vehicle' => $vehicle])
        ->set('selectedDriverId', '')
        ->call('assignDriver')
        ->assertHasErrors(['selectedDriverId' => 'required']);
});

test('assignDriver attaches the selected driver and closes the modal', function () {
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();

    Livewire::test(VehicleDriversTable::class, ['vehicle' => $vehicle])
        ->call('openAssignModal')
        ->set('selectedDriverId', (string) $driver->id)
        ->call('assignDriver')
        ->assertHasNoErrors()
        ->assertSet('showAssignModal', false)
        ->assertSet('selectedDriverId', '')
        ->assertSee($driver->name);

    expect($vehicle->drivers()->where('drivers.id', $driver->id)->exists())->toBeTrue();
});

test('assignDriver does not create a duplicate pivot row when called twice', function () {
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();

    $vehicle->drivers()->attach($driver);

    Livewire::test(VehicleDriversTable::class, ['vehicle' => $vehicle])
        ->set('selectedDriverId', (string) $driver->id)
        ->call('assignDriver');

    expect($vehicle->drivers()->where('drivers.id', $driver->id)->count())->toBe(1);
});
