<?php

use App\Enums\DeliveryStatusEnum;
use App\Livewire\Tables\DeliveriesTable;
use App\Models\Delivery;
use Livewire\Livewire;

beforeEach(function () {
    actingAsAdmin();
});

test('guest is redirected from the deliveries list', function () {
    auth()->logout();

    $this->get(route('deliveries.index'))->assertRedirect(route('login'));
});

test('deliveries index page lists deliveries', function () {
    Delivery::factory()->create(['number' => 'DEL-0001-AAA']);

    $this->get(route('deliveries.index'))->assertOk()->assertSee('DEL-0001-AAA');
});

test('search filters deliveries by number', function () {
    Delivery::factory()->create(['number' => 'DEL-0001-AAA']);
    Delivery::factory()->create(['number' => 'DEL-0002-BBB']);

    Livewire::test(DeliveriesTable::class)
        ->set('search', 'DEL-0001-AAA')
        ->assertSee('DEL-0001-AAA')
        ->assertDontSee('DEL-0002-BBB');
});

test('status filter narrows the list to a single status', function () {
    Delivery::factory()->create(['number' => 'DEL-PLANNED', 'status' => DeliveryStatusEnum::PLANNED]);
    Delivery::factory()->create(['number' => 'DEL-COMPLETED', 'status' => DeliveryStatusEnum::COMPLETED]);

    Livewire::test(DeliveriesTable::class)
        ->set('status', (string) DeliveryStatusEnum::PLANNED->value)
        ->assertSee('DEL-PLANNED')
        ->assertDontSee('DEL-COMPLETED');
});

test('trashed filter shows only soft deleted deliveries', function () {
    Delivery::factory()->create(['number' => 'DEL-KEEPME']);
    $deleted = Delivery::factory()->create(['number' => 'DEL-DELETED']);
    $deleted->delete();

    Livewire::test(DeliveriesTable::class)
        ->set('trashed', 'only')
        ->assertSee('DEL-DELETED')
        ->assertDontSee('DEL-KEEPME');
});

test('deleteDelivery removes a single delivery', function () {
    $delivery = Delivery::factory()->create(['status' => DeliveryStatusEnum::COMPLETED]);

    Livewire::test(DeliveriesTable::class)->call('deleteDelivery', $delivery->id);

    $this->assertSoftDeleted($delivery);
});

test('deleteDelivery refuses to delete a delivery that is currently active', function () {
    $delivery = Delivery::factory()->create(['status' => DeliveryStatusEnum::IN_PROGRESS]);

    Livewire::test(DeliveriesTable::class)->call('deleteDelivery', $delivery->id);

    $this->assertNotSoftDeleted($delivery);
});

test('deleteSelected soft deletes all selected deliveries', function () {
    $deliveries = Delivery::factory()->count(3)->create(['status' => DeliveryStatusEnum::COMPLETED]);

    Livewire::test(DeliveriesTable::class)
        ->set('selected', $deliveries->pluck('id')->toArray())
        ->call('deleteSelected');

    $deliveries->each(fn (Delivery $delivery) => $this->assertSoftDeleted($delivery));
});

test('deleteSelected refuses a selection containing an active delivery', function () {
    $completed = Delivery::factory()->create(['status' => DeliveryStatusEnum::COMPLETED]);
    $active = Delivery::factory()->create(['status' => DeliveryStatusEnum::ASSIGNED]);

    Livewire::test(DeliveriesTable::class)
        ->set('selected', [$completed->id, $active->id])
        ->call('deleteSelected');

    $this->assertNotSoftDeleted($completed);
    $this->assertNotSoftDeleted($active);
});

test('restoreDelivery restores a soft deleted delivery', function () {
    $delivery = Delivery::factory()->create();
    $delivery->delete();

    Livewire::test(DeliveriesTable::class)->call('restoreDelivery', $delivery->id);

    expect($delivery->fresh()->trashed())->toBeFalse();
});

test('forceDeleteDelivery permanently deletes a soft deleted delivery', function () {
    $delivery = Delivery::factory()->create();
    $delivery->delete();

    Livewire::test(DeliveriesTable::class)->call('forceDeleteDelivery', $delivery->id);

    $this->assertDatabaseMissing('deliveries', ['id' => $delivery->id]);
});
