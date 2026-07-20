<?php

use App\Livewire\Tables\ContractorsTable;
use App\Models\Contractor;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('guest is redirected from the contractors list', function () {
    auth()->logout();

    $this->get(route('contractors.index'))->assertRedirect(route('login'));
});

test('contractors index page lists contractors', function () {
    Contractor::factory()->create(['name' => 'Acme Sp. z o.o.']);

    $this->get(route('contractors.index'))->assertOk()->assertSee('Acme Sp. z o.o.');
});

test('search filters contractors by name', function () {
    Contractor::factory()->create(['name' => 'Acme Sp. z o.o.']);
    Contractor::factory()->create(['name' => 'Globex S.A.']);

    Livewire::test(ContractorsTable::class)
        ->set('search', 'Acme')
        ->assertSee('Acme Sp. z o.o.')
        ->assertDontSee('Globex S.A.');
});

test('active filter narrows the list to active or inactive contractors', function () {
    Contractor::factory()->create(['name' => 'Active Contractor', 'active' => true]);
    Contractor::factory()->create(['name' => 'Inactive Contractor', 'active' => false]);

    Livewire::test(ContractorsTable::class)
        ->set('active', '1')
        ->assertSee('Active Contractor')
        ->assertDontSee('Inactive Contractor');
});

test('toggleActive flips the active flag', function () {
    $contractor = Contractor::factory()->create(['active' => true]);

    Livewire::test(ContractorsTable::class)->call('toggleActive', $contractor->id);

    expect($contractor->refresh()->active)->toBeFalse();
});

test('deleteSelected removes all selected contractors', function () {
    $contractors = Contractor::factory()->count(3)->create();

    Livewire::test(ContractorsTable::class)
        ->set('selected', $contractors->pluck('id')->toArray())
        ->call('deleteSelected');

    $contractors->each(fn (Contractor $contractor) => $this->assertSoftDeleted($contractor));
});

test('trashed filter can include soft deleted contractors', function () {
    $contractor = Contractor::factory()->create(['name' => 'Deleted Contractor']);
    $contractor->delete();

    Livewire::test(ContractorsTable::class)
        ->assertDontSee('Deleted Contractor')
        ->set('trashed', 'with')
        ->assertSee('Deleted Contractor');
});
