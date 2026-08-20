<?php

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Livewire\Calendars\DeliveriesCalendar;
use App\Models\Delivery;
use App\Models\DeliveryTransportSet;
use App\Models\Driver;
use Livewire\Livewire;

beforeEach(function () {
    actingAsAdmin();
});

test('getEvents returns transport sets scheduled within the given range', function () {
    $driver = Driver::factory()->create(['name' => 'Jan Kowalski']);
    $delivery = Delivery::factory()->create(['number' => 'DEL-CAL-1']);
    $transportSet = DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'driver_id' => $driver->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
        'loading_at' => today()->setTime(9, 0),
        'unloading_at' => today()->setTime(13, 0),
    ]);

    $component = Livewire::test(DeliveriesCalendar::class)->instance();
    $result = $component->getEvents(today()->startOfDay()->toDateTimeString(), today()->endOfDay()->toDateTimeString());

    expect($result)->toHaveCount(1);
    expect($result[0]['id'])->toBe($transportSet->id);
    expect($result[0]['title'])->toBe('DEL-CAL-1 · Jan Kowalski');
});

test('getEvents excludes draft and cancelled transport sets', function () {
    $delivery = Delivery::factory()->create();
    DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'status' => DeliveryTransportSetStatusEnum::DRAFT,
        'loading_at' => today()->setTime(9, 0),
        'unloading_at' => today()->setTime(10, 0),
    ]);
    DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'status' => DeliveryTransportSetStatusEnum::CANCELLED,
        'loading_at' => today()->setTime(9, 0),
        'unloading_at' => today()->setTime(10, 0),
    ]);

    $component = Livewire::test(DeliveriesCalendar::class)->instance();
    $result = $component->getEvents(today()->startOfDay()->toDateTimeString(), today()->endOfDay()->toDateTimeString());

    expect($result)->toBeEmpty();
});

test('openTransportSet dispatches the shared open-transport-set-modal event instead of opening its own modal', function () {
    $transportSet = DeliveryTransportSet::factory()->create();

    Livewire::test(DeliveriesCalendar::class)
        ->call('openTransportSet', $transportSet->id, '2026-01-01 10:00', '2026-01-01 12:00')
        ->assertDispatched(
            'open-transport-set-modal',
            transportSetId: $transportSet->id,
            loadingAt: '2026-01-01 10:00',
            unloadingAt: '2026-01-01 12:00',
        );
});
