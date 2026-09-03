<?php

use App\Enums\DeliveryStatusEnum;
use App\Livewire\Tables\ContractorsTable;
use App\Models\Contractor;
use App\Models\Delivery;
use Livewire\Livewire;

beforeEach(function () {
    actingAsAdmin();
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

test('deleteContractor removes a single contractor', function () {
    $contractor = Contractor::factory()->create();

    Livewire::test(ContractorsTable::class)->call('deleteContractor', $contractor->id);

    $this->assertSoftDeleted($contractor);
});

test('deleteContractor refuses to delete a contractor with an active delivery', function () {
    $contractor = Contractor::factory()->create();
    Delivery::factory()->create(['contractor_id' => $contractor->id, 'status' => DeliveryStatusEnum::IN_PROGRESS]);

    Livewire::test(ContractorsTable::class)->call('deleteContractor', $contractor->id);

    $this->assertNotSoftDeleted($contractor);
});

test('deleteContractor allows deleting a contractor whose delivery is completed', function () {
    $contractor = Contractor::factory()->create();
    Delivery::factory()->create(['contractor_id' => $contractor->id, 'status' => DeliveryStatusEnum::COMPLETED]);

    Livewire::test(ContractorsTable::class)->call('deleteContractor', $contractor->id);

    $this->assertSoftDeleted($contractor);
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

test('restoreContractor restores a soft deleted contractor', function () {
    $contractor = Contractor::factory()->create();
    $contractor->delete();

    Livewire::test(ContractorsTable::class)->call('restoreContractor', $contractor->id);

    expect($contractor->fresh()->trashed())->toBeFalse();
});

test('forceDeleteContractor permanently deletes a soft deleted contractor', function () {
    $contractor = Contractor::factory()->create();
    $contractor->delete();

    Livewire::test(ContractorsTable::class)->call('forceDeleteContractor', $contractor->id);

    $this->assertDatabaseMissing('contractors', ['id' => $contractor->id]);
});

test('forceDeleteContractor nulls the delivery reference instead of deleting the delivery', function () {
    $contractor = Contractor::factory()->create();
    $delivery = Delivery::factory()->create(['contractor_id' => $contractor->id, 'status' => DeliveryStatusEnum::COMPLETED]);
    $contractor->delete();

    Livewire::test(ContractorsTable::class)->call('forceDeleteContractor', $contractor->id);

    $this->assertDatabaseMissing('contractors', ['id' => $contractor->id]);
    $this->assertDatabaseHas('deliveries', ['id' => $delivery->id, 'contractor_id' => null]);
});

