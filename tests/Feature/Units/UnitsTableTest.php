<?php

use App\Livewire\Tables\UnitsTable;
use App\Models\Good;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('guest is redirected from the units list', function () {
    auth()->logout();

    $this->get(route('units.index'))->assertRedirect(route('login'));
});

test('units index page lists units', function () {
    Unit::factory()->create(['name' => 'kg']);

    $this->get(route('units.index'))->assertOk()->assertSee('kg');
});

test('search filters units by name', function () {
    Unit::factory()->create(['name' => 'kg']);
    Unit::factory()->create(['name' => 'litr']);

    Livewire::test(UnitsTable::class)
        ->set('search', 'kg')
        ->assertSee('kg')
        ->assertDontSee('litr');
});

test('active filter narrows the list to active or inactive units', function () {
    Unit::factory()->create(['name' => 'active-unit', 'is_active' => true]);
    Unit::factory()->create(['name' => 'inactive-unit', 'is_active' => false]);

    Livewire::test(UnitsTable::class)
        ->set('isActive', '1')
        ->assertSee('active-unit')
        ->assertDontSee('inactive-unit');
});

test('trashed filter shows only soft deleted units', function () {
    Unit::factory()->create(['name' => 'keepme']);
    $deleted = Unit::factory()->create(['name' => 'deletedunit']);
    $deleted->delete();

    Livewire::test(UnitsTable::class)
        ->set('trashed', 'only')
        ->assertSee('deletedunit')
        ->assertDontSee('keepme');
});

test('toggleActive flips the is_active flag', function () {
    $unit = Unit::factory()->create(['is_active' => true]);

    Livewire::test(UnitsTable::class)->call('toggleActive', $unit->id);

    expect($unit->refresh()->is_active)->toBeFalse();
});

test('deleteUnit removes a single unit', function () {
    $unit = Unit::factory()->create();

    Livewire::test(UnitsTable::class)->call('deleteUnit', $unit->id);

    $this->assertSoftDeleted($unit);
});

test('deleteSelected soft deletes all selected units', function () {
    $units = Unit::factory()->count(3)->create();

    Livewire::test(UnitsTable::class)
        ->set('selected', $units->pluck('id')->toArray())
        ->call('deleteSelected');

    $units->each(fn (Unit $unit) => $this->assertSoftDeleted($unit));
});

test('when scoped to a good, table only shows assigned units', function () {
    $good = Good::factory()->create();
    $assigned = Unit::factory()->create(['name' => 'assigned-unit']);
    $unassigned = Unit::factory()->create(['name' => 'unassigned-unit']);
    $good->units()->attach($assigned);

    Livewire::test(UnitsTable::class, ['good' => $good])
        ->assertSeeHtml('unit-row-'.$assigned->id)
        ->assertDontSeeHtml('unit-row-'.$unassigned->id);
});

test('when scoped to a good, assignable unit options exclude already assigned units and assigning attaches it', function () {
    $good = Good::factory()->create();
    $assigned = Unit::factory()->create(['name' => 'assigned-unit']);
    $unassigned = Unit::factory()->create(['name' => 'unassigned-unit']);
    $good->units()->attach($assigned);

    $component = Livewire::test(UnitsTable::class, ['good' => $good]);
    $options = $component->get('assignableUnitOptions');

    expect($options)->toHaveKey($unassigned->id)
        ->and($options)->not->toHaveKey($assigned->id);

    $component
        ->set('selectedUnitId', $unassigned->id)
        ->call('assignUnit');

    expect($good->units()->pluck('unit_id')->all())
        ->toContain($assigned->id, $unassigned->id);
});

test('when scoped to a good, deleting a unit only detaches it', function () {
    $good = Good::factory()->create();
    $unit = Unit::factory()->create();
    $good->units()->attach($unit);

    Livewire::test(UnitsTable::class, ['good' => $good])
        ->call('deleteUnit', $unit->id);

    expect($good->units()->count())->toBe(0);
    $this->assertNotSoftDeleted($unit);
});
