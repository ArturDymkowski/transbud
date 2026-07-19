<?php

use App\Enums\VehicleTypeEnum;
use App\Livewire\Forms\VehiclesForm;
use App\Models\User;
use App\Models\Vehicle;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

function validVehiclePayload(): array
{
    return [
        'vehicleData.registration_number' => 'WA12345',
        'vehicleData.vin' => '1HGCM82633A004352',
        'vehicleData.type' => VehicleTypeEnum::TRACTOR->value,
    ];
}

test('vehicle edit page renders the edit and assigned drivers tabs', function () {
    $vehicle = Vehicle::factory()->create();

    $this->get(route('vehicles.edit', $vehicle))
        ->assertOk()
        ->assertSee(trans('labels.tables.edit'))
        ->assertSee(trans('vehicles.assigned_drivers'))
        ->assertSee(trans('vehicles.assigned_drivers_empty_title'));
});

test('required fields are validated on create', function () {
    Livewire::test(VehiclesForm::class)
        ->set('vehicleData.registration_number', '')
        ->set('vehicleData.vin', '')
        ->set('vehicleData.type', '')
        ->call('save')
        ->assertHasErrors([
            'vehicleData.registration_number' => 'required',
            'vehicleData.vin' => 'required',
            'vehicleData.type' => 'required',
        ]);

    $this->assertDatabaseCount('vehicles', 0);
});

test('registration number must be unique among vehicles', function () {
    Vehicle::factory()->create(['registration_number' => 'WA12345']);

    Livewire::test(VehiclesForm::class)
        ->set(validVehiclePayload())
        ->set('vehicleData.vin', 'DIFFERENTVIN0001')
        ->call('save')
        ->assertHasErrors(['vehicleData.registration_number' => 'unique']);
});

test('vin must be unique among vehicles', function () {
    Vehicle::factory()->create(['vin' => '1HGCM82633A004352']);

    Livewire::test(VehiclesForm::class)
        ->set(validVehiclePayload())
        ->set('vehicleData.registration_number', 'DIFFERENT1')
        ->call('save')
        ->assertHasErrors(['vehicleData.vin' => 'unique']);
});

test('a new vehicle can be created with valid data', function () {
    Livewire::test(VehiclesForm::class)
        ->set(validVehiclePayload())
        ->set('vehicleData.technical_inspection_expiry_date', now()->addYear()->toDateString())
        ->set('vehicleData.additional_notes', 'Some notes')
        ->call('save')
        ->assertRedirect(route('vehicles.index'));

    $this->assertDatabaseHas('vehicles', [
        'registration_number' => 'WA12345',
        'vin' => '1HGCM82633A004352',
        'type' => VehicleTypeEnum::TRACTOR->value,
        'additional_notes' => 'Some notes',
    ]);

    expect(session('success'))->toBe(trans('labels.general.saved_success'));
});

test('an existing vehicle can be edited', function () {
    // ->fresh() forces a DB round-trip so columns come back as plain values,
    // matching what route-model binding gives the real controller/component.
    $vehicle = Vehicle::factory()->create(['registration_number' => 'OLD12345'])->fresh();

    Livewire::test(VehiclesForm::class, ['vehicle' => $vehicle])
        ->set('vehicleData.registration_number', 'NEW12345')
        ->call('save')
        ->assertRedirect(route('vehicles.index'));

    expect($vehicle->refresh()->registration_number)->toBe('NEW12345');
    expect(session('success'))->toBe(trans('labels.general.updated_success'));
});

test('editing a vehicle keeps its own registration number and vin valid despite the uniqueness rule', function () {
    $vehicle = Vehicle::factory()->create([
        'registration_number' => 'WA12345',
        'vin' => '1HGCM82633A004352',
    ])->fresh();

    Livewire::test(VehiclesForm::class, ['vehicle' => $vehicle])
        ->set('vehicleData.additional_notes', 'Updated notes')
        ->call('save')
        ->assertHasNoErrors(['vehicleData.registration_number', 'vehicleData.vin'])
        ->assertRedirect(route('vehicles.index'));
});

test('type defaults to the first option so creating without touching the field succeeds', function () {
    Livewire::test(VehiclesForm::class)
        ->assertSet('vehicleData.type', VehicleTypeEnum::TRACTOR->value)
        ->set('vehicleData.registration_number', 'WA12345')
        ->set('vehicleData.vin', '1HGCM82633A004352')
        ->call('save')
        ->assertHasNoErrors('vehicleData.type')
        ->assertRedirect(route('vehicles.index'));

    $this->assertDatabaseHas('vehicles', [
        'registration_number' => 'WA12345',
        'type' => VehicleTypeEnum::TRACTOR->value,
    ]);
});

test('type must be a valid vehicle type', function () {
    Livewire::test(VehiclesForm::class)
        ->set(validVehiclePayload())
        ->set('vehicleData.type', 99)
        ->call('save')
        ->assertHasErrors(['vehicleData.type']);
});
