<?php

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Enums\VehicleTypeEnum;
use App\Livewire\Forms\DeliveriesForm;
use App\Livewire\Modals\TransportSetEditModal;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Delivery;
use App\Models\DeliveryTransportSet;
use App\Models\Driver;
use App\Models\Vehicle;
use Livewire\Livewire;

beforeEach(function () {
    actingAsAdmin();
});

function createDeliveryWithMatchingAddressForAvailabilityTest(): Delivery
{
    $contractor = Contractor::factory()->create();
    $address = ContractorAddress::factory()->create(['contractor_id' => $contractor->id]);

    return Delivery::factory()->create([
        'contractor_id' => $contractor->id,
        'contractor_address_id' => $address->id,
    ]);
}

test('DeliveriesForm allows two overlapping transport sets in the same delivery to share a driver, vehicle and trailer', function () {
    $delivery = createDeliveryWithMatchingAddressForAvailabilityTest();
    $driver = Driver::factory()->create();
    $tractor = Vehicle::factory()->create(['type' => VehicleTypeEnum::TRACTOR->value]);
    $trailer = Vehicle::factory()->create(['type' => VehicleTypeEnum::TRAILER->value]);

    DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'driver_id' => $driver->id,
        'vehicle_id' => $tractor->id,
        'trailer_id' => $trailer->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
        'loading_at' => now(),
        'unloading_at' => now()->addDay(),
    ]);
    DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'driver_id' => $driver->id,
        'vehicle_id' => $tractor->id,
        'trailer_id' => $trailer->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
        'loading_at' => now()->addHours(12),
        'unloading_at' => now()->addDays(2),
    ]);

    Livewire::test(DeliveriesForm::class, ['delivery' => $delivery])
        ->call('save')
        ->assertHasNoErrors();
});

test('DeliveriesForm refuses a driver, vehicle and trailer already busy on another delivery', function () {
    $driver = Driver::factory()->create();
    $tractor = Vehicle::factory()->create(['type' => VehicleTypeEnum::TRACTOR->value]);
    $trailer = Vehicle::factory()->create(['type' => VehicleTypeEnum::TRAILER->value]);

    $otherDelivery = createDeliveryWithMatchingAddressForAvailabilityTest();
    DeliveryTransportSet::factory()->create([
        'delivery_id' => $otherDelivery->id,
        'driver_id' => $driver->id,
        'vehicle_id' => $tractor->id,
        'trailer_id' => $trailer->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
        'loading_at' => now(),
        'unloading_at' => now()->addDay(),
    ]);

    $myDelivery = createDeliveryWithMatchingAddressForAvailabilityTest();
    DeliveryTransportSet::factory()->create([
        'delivery_id' => $myDelivery->id,
        'driver_id' => $driver->id,
        'vehicle_id' => $tractor->id,
        'trailer_id' => $trailer->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
        'loading_at' => now()->addHours(12),
        'unloading_at' => now()->addDays(2),
    ]);

    Livewire::test(DeliveriesForm::class, ['delivery' => $myDelivery])
        ->call('save')
        ->assertHasErrors([
            'transportSetsData.0.driver_id',
            'transportSetsData.0.vehicle_id',
            'transportSetsData.0.trailer_id',
        ]);
});

test('TransportSetEditModal allows two overlapping transport sets in the same delivery to share a driver, vehicle and trailer', function () {
    $delivery = createDeliveryWithMatchingAddressForAvailabilityTest();
    $driver = Driver::factory()->create();
    $tractor = Vehicle::factory()->create(['type' => VehicleTypeEnum::TRACTOR->value]);
    $trailer = Vehicle::factory()->create(['type' => VehicleTypeEnum::TRAILER->value]);

    DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'driver_id' => $driver->id,
        'vehicle_id' => $tractor->id,
        'trailer_id' => $trailer->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
        'loading_at' => now(),
        'unloading_at' => now()->addDay(),
    ]);
    $transportSet = DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'driver_id' => $driver->id,
        'vehicle_id' => $tractor->id,
        'trailer_id' => $trailer->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
        'loading_at' => now()->addHours(12),
        'unloading_at' => now()->addDays(2),
    ]);

    Livewire::test(TransportSetEditModal::class)
        ->dispatch('open-transport-set-modal', transportSetId: $transportSet->id)
        ->call('save')
        ->assertHasNoErrors();
});

test('TransportSetEditModal refuses a driver, vehicle and trailer already busy on another delivery', function () {
    $driver = Driver::factory()->create();
    $tractor = Vehicle::factory()->create(['type' => VehicleTypeEnum::TRACTOR->value]);
    $trailer = Vehicle::factory()->create(['type' => VehicleTypeEnum::TRAILER->value]);

    $otherDelivery = createDeliveryWithMatchingAddressForAvailabilityTest();
    DeliveryTransportSet::factory()->create([
        'delivery_id' => $otherDelivery->id,
        'driver_id' => $driver->id,
        'vehicle_id' => $tractor->id,
        'trailer_id' => $trailer->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
        'loading_at' => now(),
        'unloading_at' => now()->addDay(),
    ]);

    $myDelivery = createDeliveryWithMatchingAddressForAvailabilityTest();
    $transportSet = DeliveryTransportSet::factory()->create([
        'delivery_id' => $myDelivery->id,
        'driver_id' => $driver->id,
        'vehicle_id' => $tractor->id,
        'trailer_id' => $trailer->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
        'loading_at' => now()->addHours(12),
        'unloading_at' => now()->addDays(2),
    ]);

    Livewire::test(TransportSetEditModal::class)
        ->dispatch('open-transport-set-modal', transportSetId: $transportSet->id)
        ->call('save')
        ->assertHasErrors([
            'transportSetData.driver_id',
            'transportSetData.vehicle_id',
            'transportSetData.trailer_id',
        ]);
});
