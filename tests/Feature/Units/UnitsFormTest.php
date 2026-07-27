<?php

use App\Livewire\Forms\UnitsForm;
use App\Models\Unit;
use Livewire\Livewire;

beforeEach(function () {
    actingAsAdmin();
});

test('required fields are validated on create', function () {
    Livewire::test(UnitsForm::class)
        ->set('unitData.name', '')
        ->call('save')
        ->assertHasErrors([
            'unitData.name' => 'required',
        ]);

    $this->assertDatabaseCount('units', 0);
});

test('name must be unique among units', function () {
    Unit::factory()->create(['name' => 'kg']);

    Livewire::test(UnitsForm::class)
        ->set('unitData.name', 'kg')
        ->call('save')
        ->assertHasErrors(['unitData.name' => 'unique']);
});

test('a new unit can be created with valid data', function () {
    Livewire::test(UnitsForm::class)
        ->set('unitData.name', 'kg')
        ->call('save')
        ->assertRedirect(route('units.index'));

    $this->assertDatabaseHas('units', ['name' => 'kg']);

    expect(session('success'))->toBe(trans('labels.general.saved_success'));
});

test('an existing unit can be edited', function () {
    $unit = Unit::factory()->create(['name' => 'old'])->fresh();

    Livewire::test(UnitsForm::class, ['unit' => $unit])
        ->set('unitData.name', 'new')
        ->call('save')
        ->assertRedirect(route('units.index'));

    expect($unit->refresh()->name)->toBe('new');
    expect(session('success'))->toBe(trans('labels.general.updated_success'));
});

test('editing a unit keeps its own name valid despite the uniqueness rule', function () {
    $unit = Unit::factory()->create(['name' => 'kg'])->fresh();

    Livewire::test(UnitsForm::class, ['unit' => $unit])
        ->set('unitData.name', 'kg')
        ->call('save')
        ->assertHasNoErrors(['unitData.name'])
        ->assertRedirect(route('units.index'));
});
