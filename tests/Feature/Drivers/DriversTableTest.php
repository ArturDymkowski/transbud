<?php

use App\Enums\CountriesEnum;
use App\Livewire\Tables\DriversTable;
use App\Models\Driver;
use App\Models\User;
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
