<?php

use App\Livewire\Forms\GoodsForm;
use App\Models\Good;
use App\Models\Unit;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

function validGoodPayload(): array
{
    return [
        'goodData.name' => 'Cement',
        'goodData.default_unit_id' => Unit::factory()->create()->id,
    ];
}

test('good edit page renders the edit and units tabs', function () {
    $good = Good::factory()->create();

    $this->get(route('goods.edit', $good))
        ->assertOk()
        ->assertSee(trans('labels.tables.edit'))
        ->assertSee(trans('goods.units'))
        ->assertSee(trans('goods.assign_unit'));
});

test('required fields are validated on create', function () {
    Livewire::test(GoodsForm::class)
        ->set('goodData.name', '')
        ->set('goodData.default_unit_id', '')
        ->call('save')
        ->assertHasErrors([
            'goodData.name' => 'required',
            'goodData.default_unit_id' => 'required',
        ]);

    $this->assertDatabaseCount('goods', 0);
});

test('default unit must exist', function () {
    Livewire::test(GoodsForm::class)
        ->set('goodData.name', 'Cement')
        ->set('goodData.default_unit_id', 999)
        ->call('save')
        ->assertHasErrors(['goodData.default_unit_id' => 'exists']);
});

test('a new good can be created with valid data', function () {
    $unit = Unit::factory()->create(['name' => 'kg']);

    Livewire::test(GoodsForm::class)
        ->set('goodData.name', 'Cement')
        ->set('goodData.default_unit_id', $unit->id)
        ->set('goodData.description', 'Some notes')
        ->call('save')
        ->assertRedirect(route('goods.index'));

    $this->assertDatabaseHas('goods', [
        'name' => 'Cement',
        'default_unit_id' => $unit->id,
        'description' => 'Some notes',
    ]);

    expect(session('success'))->toBe(trans('labels.general.saved_success'));
});

test('an existing good can be edited', function () {
    $good = Good::factory()->create(['name' => 'Old name'])->fresh();

    Livewire::test(GoodsForm::class, ['good' => $good])
        ->set('goodData.name', 'New name')
        ->call('save')
        ->assertRedirect(route('goods.index'));

    expect($good->refresh()->name)->toBe('New name');
    expect(session('success'))->toBe(trans('labels.general.updated_success'));
});

test('default unit defaults to the first option so creating without touching the field succeeds', function () {
    $unit = Unit::factory()->create();

    Livewire::test(GoodsForm::class)
        ->assertSet('goodData.default_unit_id', $unit->id)
        ->set('goodData.name', 'Cement')
        ->call('save')
        ->assertHasNoErrors('goodData.default_unit_id')
        ->assertRedirect(route('goods.index'));
});
