<?php

use App\Enums\CountriesEnum;
use App\Livewire\Tables\DriversTable;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('guest is redirected from the drivers list', function () {
    auth()->logout();

    $this->get(route('drivers.index'))->assertRedirect(route('login'));
});

test('drivers index page lists drivers', function () {
    $driver = Driver::factory()->create(['name' => 'Jan Kowalski']);

    $this->get(route('drivers.index'))->assertOk()->assertSee('Jan Kowalski');
});

test('search filters drivers by name', function () {
    Driver::factory()->create(['name' => 'Jan Kowalski']);
    Driver::factory()->create(['name' => 'Anna Nowak']);

    Livewire::test(DriversTable::class)
        ->set('search', 'Kowalski')
        ->assertSee('Jan Kowalski')
        ->assertDontSee('Anna Nowak');
});

test('active filter narrows the list to active or inactive drivers', function () {
    Driver::factory()->create(['name' => 'Active Driver', 'is_active' => true]);
    Driver::factory()->create(['name' => 'Inactive Driver', 'is_active' => false]);

    Livewire::test(DriversTable::class)
        ->set('isActive', '1')
        ->assertSee('Active Driver')
        ->assertDontSee('Inactive Driver');
});

test('country filter narrows the list', function () {
    Driver::factory()->create(['name' => 'Polish Driver', 'country' => CountriesEnum::POLAND->value]);
    Driver::factory()->create(['name' => 'German Driver', 'country' => CountriesEnum::GERMANY->value]);

    Livewire::test(DriversTable::class)
        ->set('country', CountriesEnum::POLAND->value)
        ->assertSee('Polish Driver')
        ->assertDontSee('German Driver');
});

test('toggleActive flips the is_active flag', function () {
    $driver = Driver::factory()->create(['is_active' => true]);

    Livewire::test(DriversTable::class)->call('toggleActive', $driver->id);

    expect($driver->refresh()->is_active)->toBeFalse();
});

test('deleteDriver removes a single driver', function () {
    $driver = Driver::factory()->create();

    Livewire::test(DriversTable::class)->call('deleteDriver', $driver->id);

    $this->assertSoftDeleted($driver);
});

test('deleteSelected removes all selected drivers', function () {
    $drivers = Driver::factory()->count(3)->create();

    Livewire::test(DriversTable::class)
        ->set('selected', $drivers->pluck('id')->toArray())
        ->call('deleteSelected');

    $drivers->each(fn (Driver $driver) => $this->assertSoftDeleted($driver));
});

test('date range filters show a labeled badge above the table', function () {
    $component = Livewire::test(DriversTable::class)
        ->set('drivingLicenseExpiryDateFrom', '2026-01-01')
        ->set('identityCardExpiryDateTo', '2026-12-31');

    $labels = collect($component->get('activeFilters'))->pluck('label');

    expect($labels)->toContain('Data wygaśnięcia prawa jazdy od: 2026-01-01')
        ->toContain('Data wygaśnięcia dowodu osobistego do: 2026-12-31');
});

// Scoped to a vehicle (embedded on the vehicle edit page's "Przypisani kierowcy" tab)

test('scoped to a vehicle, it lists only drivers assigned to that vehicle', function () {
    $vehicle = Vehicle::factory()->create();
    $otherVehicle = Vehicle::factory()->create();

    $assignedDriver = Driver::factory()->create(['name' => 'Jan Kowalski']);
    $otherDriver = Driver::factory()->create(['name' => 'Adam Nowak']);

    $vehicle->drivers()->attach($assignedDriver);
    $otherVehicle->drivers()->attach($otherDriver);

    Livewire::test(DriversTable::class, ['vehicle' => $vehicle])
        ->assertSeeHtml('driver-row-'.$assignedDriver->id)
        ->assertDontSeeHtml('driver-row-'.$otherDriver->id);
});

test('scoped to a vehicle, search only matches drivers assigned to that vehicle', function () {
    $vehicle = Vehicle::factory()->create();
    $otherVehicle = Vehicle::factory()->create();

    $matchingDriver = Driver::factory()->create(['name' => 'Jan Kowalski']);
    $otherVehicleDriver = Driver::factory()->create(['name' => 'Jan Nowicki']);

    $vehicle->drivers()->attach($matchingDriver);
    $otherVehicle->drivers()->attach($otherVehicleDriver);

    Livewire::test(DriversTable::class, ['vehicle' => $vehicle])
        ->set('search', 'Jan')
        ->assertSeeHtml('driver-row-'.$matchingDriver->id)
        ->assertDontSeeHtml('driver-row-'.$otherVehicleDriver->id);
});

test('scoped to a vehicle, only the id and name columns are shown', function () {
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();
    $vehicle->drivers()->attach($driver);

    // driving_license/identity_card labels are intentionally not checked here:
    // they still legitimately appear in the (kept) filter bar date-range pickers.
    Livewire::test(DriversTable::class, ['vehicle' => $vehicle])
        ->assertSee(trans('drivers.name'))
        ->assertDontSee(trans('drivers.phone'))
        ->assertDontSee(trans('drivers.pesel'));
});

test('unscoped, all driver columns are still shown', function () {
    Livewire::test(DriversTable::class)
        ->assertSee(trans('drivers.phone'))
        ->assertSee(trans('drivers.pesel'));
});

test('scoped to a vehicle, deleteDriver detaches the assignment instead of deleting the driver', function () {
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();
    $vehicle->drivers()->attach($driver);

    Livewire::test(DriversTable::class, ['vehicle' => $vehicle])
        ->call('deleteDriver', $driver->id)
        ->assertDontSeeHtml('driver-row-'.$driver->id);

    expect($vehicle->drivers()->where('drivers.id', $driver->id)->exists())->toBeFalse();
    $this->assertDatabaseHas('drivers', ['id' => $driver->id, 'deleted_at' => null]);
});

test('scoped to a vehicle, deleteSelected detaches selected drivers instead of deleting them', function () {
    $vehicle = Vehicle::factory()->create();
    $drivers = Driver::factory()->count(2)->create();
    $vehicle->drivers()->attach($drivers);

    Livewire::test(DriversTable::class, ['vehicle' => $vehicle])
        ->set('selected', $drivers->pluck('id')->toArray())
        ->call('deleteSelected');

    expect($vehicle->drivers()->count())->toBe(0);
    $drivers->each(fn (Driver $driver) => $this->assertDatabaseHas('drivers', ['id' => $driver->id, 'deleted_at' => null]));
});

test('scoped to a vehicle, assignable driver options exclude already assigned drivers', function () {
    $vehicle = Vehicle::factory()->create();
    $assigned = Driver::factory()->create(['name' => 'Jan Kowalski']);
    $unassigned = Driver::factory()->create(['name' => 'Adam Nowak']);
    $vehicle->drivers()->attach($assigned);

    $options = Livewire::test(DriversTable::class, ['vehicle' => $vehicle])
        ->get('assignableDriverOptions');

    expect($options)->toHaveKey($unassigned->id)
        ->and($options)->not->toHaveKey($assigned->id);
});

test('scoped to a vehicle, openAssignModal opens the modal', function () {
    $vehicle = Vehicle::factory()->create();

    Livewire::test(DriversTable::class, ['vehicle' => $vehicle])
        ->assertSet('showAssignModal', false)
        ->call('openAssignModal')
        ->assertSet('showAssignModal', true);
});

test('scoped to a vehicle, assignDriver requires a driver to be selected', function () {
    $vehicle = Vehicle::factory()->create();

    Livewire::test(DriversTable::class, ['vehicle' => $vehicle])
        ->set('selectedDriverId', '')
        ->call('assignDriver')
        ->assertHasErrors(['selectedDriverId' => 'required']);
});

test('scoped to a vehicle, assignDriver attaches the selected driver and closes the modal', function () {
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();

    Livewire::test(DriversTable::class, ['vehicle' => $vehicle])
        ->call('openAssignModal')
        ->set('selectedDriverId', (string) $driver->id)
        ->call('assignDriver')
        ->assertHasNoErrors()
        ->assertSet('showAssignModal', false)
        ->assertSet('selectedDriverId', '')
        ->assertSee($driver->name);

    expect($vehicle->drivers()->where('drivers.id', $driver->id)->exists())->toBeTrue();
});

test('unscoped, assignDriver is a no-op', function () {
    Livewire::test(DriversTable::class)
        ->set('selectedDriverId', '1')
        ->call('assignDriver')
        ->assertHasNoErrors();
});
