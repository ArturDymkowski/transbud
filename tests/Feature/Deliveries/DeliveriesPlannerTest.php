<?php

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Livewire\Planners\DeliveriesPlanner;
use App\Models\Delivery;
use App\Models\DeliveryTransportSet;
use App\Models\Driver;
use Livewire\Livewire;

beforeEach(function () {
    actingAsAdmin();
});

test('planner lists only active drivers as resources', function () {
    $activeDriver = Driver::factory()->create(['is_active' => true, 'name' => 'Active Driver']);
    Driver::factory()->create(['is_active' => false, 'name' => 'Inactive Driver']);

    Livewire::test(DeliveriesPlanner::class)
        ->assertSee('Active Driver')
        ->assertDontSee('Inactive Driver');
});

test('planner shows a transport set scheduled on the selected day', function () {
    $driver = Driver::factory()->create(['is_active' => true]);
    $delivery = Delivery::factory()->create(['number' => 'DEL-PLANNER-1']);
    DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'driver_id' => $driver->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
        'loading_at' => today()->setTime(9, 0),
        'unloading_at' => today()->setTime(13, 0),
    ]);

    Livewire::test(DeliveriesPlanner::class)
        ->assertSee('DEL-PLANNER-1');
});

test('planner hides draft and cancelled transport sets, matching the calendar', function () {
    $driver = Driver::factory()->create(['is_active' => true]);

    $draftDelivery = Delivery::factory()->create(['number' => 'DEL-DRAFT']);
    DeliveryTransportSet::factory()->create([
        'delivery_id' => $draftDelivery->id,
        'driver_id' => $driver->id,
        'status' => DeliveryTransportSetStatusEnum::DRAFT,
        'loading_at' => today()->setTime(9, 0),
        'unloading_at' => today()->setTime(10, 0),
    ]);

    $cancelledDelivery = Delivery::factory()->create(['number' => 'DEL-CANCELLED']);
    DeliveryTransportSet::factory()->create([
        'delivery_id' => $cancelledDelivery->id,
        'driver_id' => $driver->id,
        'status' => DeliveryTransportSetStatusEnum::CANCELLED,
        'loading_at' => today()->setTime(11, 0),
        'unloading_at' => today()->setTime(12, 0),
    ]);

    Livewire::test(DeliveriesPlanner::class)
        ->assertDontSee('DEL-DRAFT')
        ->assertDontSee('DEL-CANCELLED');
});

test('planner does not show transport sets scheduled on other days', function () {
    $driver = Driver::factory()->create(['is_active' => true]);
    $delivery = Delivery::factory()->create(['number' => 'DEL-TOMORROW']);
    DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'driver_id' => $driver->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
        'loading_at' => today()->addDay()->setTime(9, 0),
        'unloading_at' => today()->addDay()->setTime(13, 0),
    ]);

    Livewire::test(DeliveriesPlanner::class)
        ->assertDontSee('DEL-TOMORROW')
        ->call('nextDay')
        ->assertSee('DEL-TOMORROW');
});

test('previousDay, nextDay and goToToday move the visible date', function () {
    $today = today()->toDateString();

    Livewire::test(DeliveriesPlanner::class)
        ->assertSet('date', $today)
        ->call('nextDay')
        ->assertSet('date', today()->addDay()->toDateString())
        ->call('previousDay')
        ->call('previousDay')
        ->assertSet('date', today()->subDay()->toDateString())
        ->call('goToToday')
        ->assertSet('date', $today);
});

test('an empty driver filter shows all active drivers', function () {
    Driver::factory()->count(3)->create(['is_active' => true]);

    $component = Livewire::test(DeliveriesPlanner::class);

    expect($component->instance()->resources())->toHaveCount(3);
});

test('selecting drivers in the filter narrows the resources shown', function () {
    $drivers = Driver::factory()->count(3)->create(['is_active' => true]);
    $keep = $drivers->take(2)->pluck('id')->all();

    $component = Livewire::test(DeliveriesPlanner::class)
        ->set('selectedResourceIds', $keep);

    $resources = $component->instance()->resources();

    expect($resources)->toHaveCount(2);
    expect($resources->pluck('id')->sort()->values()->all())->toBe(collect($keep)->sort()->values()->all());
});

test('the filter dropdown options always list every active driver, regardless of the current selection', function () {
    $drivers = Driver::factory()->count(3)->create(['is_active' => true]);

    $component = Livewire::test(DeliveriesPlanner::class)
        ->set('selectedResourceIds', [$drivers->first()->id]);

    expect($component->instance()->resourceOptions())->toHaveCount(3);
});

test('clicking an event dispatches the shared open-transport-set-modal event', function () {
    $driver = Driver::factory()->create(['is_active' => true]);
    $transportSet = DeliveryTransportSet::factory()->create([
        'driver_id' => $driver->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
        'loading_at' => today()->setTime(9, 0),
        'unloading_at' => today()->setTime(13, 0),
    ]);

    Livewire::test(DeliveriesPlanner::class)
        ->call('openTransportSet', $transportSet->id)
        ->assertDispatched('open-transport-set-modal', transportSetId: $transportSet->id);
});
