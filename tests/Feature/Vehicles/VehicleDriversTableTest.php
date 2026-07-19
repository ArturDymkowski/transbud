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
        ->assertSee('Jan Kowalski')
        ->assertDontSee('Adam Nowak');
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

    $vehicle->drivers()->attach(Driver::factory()->create(['name' => 'Jan Kowalski']));
    $otherVehicle->drivers()->attach(Driver::factory()->create(['name' => 'Jan Nowicki']));

    Livewire::test(VehicleDriversTable::class, ['vehicle' => $vehicle])
        ->set('search', 'Jan')
        ->assertSee('Jan Kowalski')
        ->assertDontSee('Jan Nowicki');
});

test('removeAssignment detaches the driver from the vehicle without deleting the driver', function () {
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();

    $vehicle->drivers()->attach($driver);

    Livewire::test(VehicleDriversTable::class, ['vehicle' => $vehicle])
        ->call('removeAssignment', $driver->id)
        ->assertDontSee($driver->name);

    expect($vehicle->drivers()->where('drivers.id', $driver->id)->exists())->toBeFalse();
    expect(Driver::find($driver->id))->not->toBeNull();
});
