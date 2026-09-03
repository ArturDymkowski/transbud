<?php

use App\Enums\DeliveryStatusEnum;
use App\Livewire\Tables\GoodsTable;
use App\Models\Delivery;
use App\Models\DeliveryGood;
use App\Models\DeliveryTransportSet;
use App\Models\Good;
use Livewire\Livewire;

beforeEach(function () {
    actingAsAdmin();
});

test('guest is redirected from the goods list', function () {
    auth()->logout();

    $this->get(route('goods.index'))->assertRedirect(route('login'));
});

test('goods index page lists goods', function () {
    Good::factory()->create(['name' => 'Cement']);

    $this->get(route('goods.index'))->assertOk()->assertSee('Cement');
});

test('search filters goods by name', function () {
    Good::factory()->create(['name' => 'Cement']);
    Good::factory()->create(['name' => 'Sand']);

    Livewire::test(GoodsTable::class)
        ->set('search', 'Cement')
        ->assertSee('Cement')
        ->assertDontSee('Sand');
});

test('active filter narrows the list to active or inactive goods', function () {
    Good::factory()->create(['name' => 'ActiveGood', 'is_active' => true]);
    Good::factory()->create(['name' => 'InactiveGood', 'is_active' => false]);

    Livewire::test(GoodsTable::class)
        ->set('isActive', '1')
        ->assertSee('ActiveGood')
        ->assertDontSee('InactiveGood');
});

test('trashed filter shows only soft deleted goods', function () {
    Good::factory()->create(['name' => 'KeepMe']);
    $deleted = Good::factory()->create(['name' => 'DeletedGood']);
    $deleted->delete();

    Livewire::test(GoodsTable::class)
        ->set('trashed', 'only')
        ->assertSee('DeletedGood')
        ->assertDontSee('KeepMe');
});

test('toggleActive flips the is_active flag', function () {
    $good = Good::factory()->create(['is_active' => true]);

    Livewire::test(GoodsTable::class)->call('toggleActive', $good->id);

    expect($good->refresh()->is_active)->toBeFalse();
});

test('deleteGood removes a single good', function () {
    $good = Good::factory()->create();

    Livewire::test(GoodsTable::class)->call('deleteGood', $good->id);

    $this->assertSoftDeleted($good);
});

test('deleteGood refuses to delete a good used in an active delivery', function () {
    $good = Good::factory()->create();
    $delivery = Delivery::factory()->create(['status' => DeliveryStatusEnum::ASSIGNED]);
    $transportSet = DeliveryTransportSet::factory()->create(['delivery_id' => $delivery->id]);
    DeliveryGood::factory()->create(['delivery_transport_set_id' => $transportSet->id, 'good_id' => $good->id]);

    Livewire::test(GoodsTable::class)->call('deleteGood', $good->id);

    $this->assertNotSoftDeleted($good);
});

test('deleteGood allows deleting a good whose delivery is completed', function () {
    $good = Good::factory()->create();
    $delivery = Delivery::factory()->create(['status' => DeliveryStatusEnum::COMPLETED]);
    $transportSet = DeliveryTransportSet::factory()->create(['delivery_id' => $delivery->id]);
    DeliveryGood::factory()->create(['delivery_transport_set_id' => $transportSet->id, 'good_id' => $good->id]);

    Livewire::test(GoodsTable::class)->call('deleteGood', $good->id);

    $this->assertSoftDeleted($good);
});

test('deleteSelected soft deletes all selected goods', function () {
    $goods = Good::factory()->count(3)->create();

    Livewire::test(GoodsTable::class)
        ->set('selected', $goods->pluck('id')->toArray())
        ->call('deleteSelected');

    $goods->each(fn (Good $good) => $this->assertSoftDeleted($good));
});

test('restoreGood restores a soft deleted good', function () {
    $good = Good::factory()->create();
    $good->delete();

    Livewire::test(GoodsTable::class)->call('restoreGood', $good->id);

    expect($good->fresh()->trashed())->toBeFalse();
});

test('forceDeleteGood permanently deletes a soft deleted good', function () {
    $good = Good::factory()->create();
    $good->delete();

    Livewire::test(GoodsTable::class)->call('forceDeleteGood', $good->id);

    $this->assertDatabaseMissing('goods', ['id' => $good->id]);
});

test('forceDeleteGood nulls the delivery goods reference instead of deleting the delivery', function () {
    $good = Good::factory()->create();
    $delivery = Delivery::factory()->create(['status' => DeliveryStatusEnum::COMPLETED]);
    $transportSet = DeliveryTransportSet::factory()->create(['delivery_id' => $delivery->id]);
    $deliveryGood = DeliveryGood::factory()->create(['delivery_transport_set_id' => $transportSet->id, 'good_id' => $good->id]);
    $good->delete();

    Livewire::test(GoodsTable::class)->call('forceDeleteGood', $good->id);

    $this->assertDatabaseMissing('goods', ['id' => $good->id]);
    $this->assertDatabaseHas('deliveries', ['id' => $delivery->id]);
    $this->assertDatabaseHas('delivery_goods', ['id' => $deliveryGood->id, 'good_id' => null]);
});

