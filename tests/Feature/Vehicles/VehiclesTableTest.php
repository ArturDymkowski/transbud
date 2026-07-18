<?php

use App\Enums\VehicleTypeEnum;
use App\Livewire\Tables\VehiclesTable;
use App\Models\User;
use App\Models\Vehicle;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('guest is redirected from the vehicles list', function () {
    auth()->logout();

    $this->get(route('vehicles.index'))->assertRedirect(route('login'));
});

test('vehicles index page lists vehicles', function () {
    Vehicle::factory()->create(['registration_number' => 'WA12345']);

    $this->get(route('vehicles.index'))->assertOk()->assertSee('WA12345');
});

test('search filters vehicles by registration number', function () {
    Vehicle::factory()->create(['registration_number' => 'WA12345']);
    Vehicle::factory()->create(['registration_number' => 'KR98765']);

    Livewire::test(VehiclesTable::class)
        ->set('search', 'WA12345')
        ->assertSee('WA12345')
        ->assertDontSee('KR98765');
});

test('active filter narrows the list to active or inactive vehicles', function () {
    Vehicle::factory()->create(['registration_number' => 'ACTIVE01', 'is_active' => true]);
    Vehicle::factory()->create(['registration_number' => 'INACTIVE1', 'is_active' => false]);

    Livewire::test(VehiclesTable::class)
        ->set('isActive', '1')
        ->assertSee('ACTIVE01')
        ->assertDontSee('INACTIVE1');
});

test('date range filter narrows vehicles by technical inspection expiry date', function () {
    Vehicle::factory()->create(['registration_number' => 'INRANGE1', 'technical_inspection_expiry_date' => '2026-06-15']);
    Vehicle::factory()->create(['registration_number' => 'OUTRANGE1', 'technical_inspection_expiry_date' => '2020-01-01']);

    Livewire::test(VehiclesTable::class)
        ->set('technicalInspectionExpiryDateFrom', '2026-01-01')
        ->set('technicalInspectionExpiryDateTo', '2026-12-31')
        ->assertSee('INRANGE1')
        ->assertDontSee('OUTRANGE1');
});

test('sorting by type orders vehicles', function () {
    Vehicle::factory()->create(['registration_number' => 'TRAILER1', 'type' => VehicleTypeEnum::TRAILER->value]);
    Vehicle::factory()->create(['registration_number' => 'TRACTOR1', 'type' => VehicleTypeEnum::TRACTOR->value]);

    $component = Livewire::test(VehiclesTable::class)->call('sortBy', 'type');

    expect($component->get('sortField'))->toBe('type');
    expect($component->get('sortDirection'))->toBe('asc');
});

test('trashed filter shows only soft deleted vehicles', function () {
    $active = Vehicle::factory()->create(['registration_number' => 'KEEPME1']);
    $deleted = Vehicle::factory()->create(['registration_number' => 'DELETED1']);
    $deleted->delete();

    Livewire::test(VehiclesTable::class)
        ->set('trashed', 'only')
        ->assertSee('DELETED1')
        ->assertDontSee('KEEPME1');
});

test('toggleActive flips the is_active flag', function () {
    $vehicle = Vehicle::factory()->create(['is_active' => true]);

    Livewire::test(VehiclesTable::class)->call('toggleActive', $vehicle->id);

    expect($vehicle->refresh()->is_active)->toBeFalse();
});

test('deleteSelected soft deletes all selected vehicles', function () {
    $vehicles = Vehicle::factory()->count(3)->create();

    Livewire::test(VehiclesTable::class)
        ->set('selected', $vehicles->pluck('id')->toArray())
        ->call('deleteSelected');

    $vehicles->each(fn (Vehicle $vehicle) => $this->assertSoftDeleted($vehicle));
});

test('date range filters show a labeled badge above the table', function () {
    $component = Livewire::test(VehiclesTable::class)
        ->set('insuranceExpiryDateFrom', '2026-01-01')
        ->set('tachographInspectionExpiryDateTo', '2026-12-31');

    $labels = collect($component->get('activeFilters'))->pluck('label');

    expect($labels)->toContain('Data ważności ubezpieczenia od: 2026-01-01')
        ->toContain('Data ważności legalizacji tachografu do: 2026-12-31');
});
