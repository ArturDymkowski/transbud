<?php

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Enums\VehicleTypeEnum;
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

function createDeliveryWithMatchingAddressForModal(): Delivery
{
    $contractor = Contractor::factory()->create();
    $address = ContractorAddress::factory()->create(['contractor_id' => $contractor->id]);

    return Delivery::factory()->create([
        'contractor_id' => $contractor->id,
        'contractor_address_id' => $address->id,
    ]);
}

test('the open-transport-set-modal event populates the modal with the transport set data', function () {
    $delivery = createDeliveryWithMatchingAddressForModal();
    $driver = Driver::factory()->create();
    $transportSet = DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'driver_id' => $driver->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
    ]);

    Livewire::test(TransportSetEditModal::class)
        ->dispatch('open-transport-set-modal', transportSetId: $transportSet->id)
        ->assertSet('isOpen', true)
        ->assertSet('transportSetId', $transportSet->id)
        ->assertSet('deliveryId', $delivery->id)
        ->assertSet('transportSetData.driver_id', $driver->id);
});

test('saving updates the transport set and notifies the calendar and planner', function () {
    $delivery = createDeliveryWithMatchingAddressForModal();
    $driver = Driver::factory()->create();
    $tractor = Vehicle::factory()->create(['type' => VehicleTypeEnum::TRACTOR->value]);
    $trailer = Vehicle::factory()->create(['type' => VehicleTypeEnum::TRAILER->value]);
    $transportSet = DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'status' => DeliveryTransportSetStatusEnum::DRAFT,
    ]);

    Livewire::test(TransportSetEditModal::class)
        ->dispatch('open-transport-set-modal', transportSetId: $transportSet->id)
        ->set('transportSetData.status', DeliveryTransportSetStatusEnum::ASSIGNED->value)
        ->set('transportSetData.driver_id', $driver->id)
        ->set('transportSetData.vehicle_id', $tractor->id)
        ->set('transportSetData.trailer_id', $trailer->id)
        ->set('transportSetData.loading_at', '2026-01-01 08:00')
        ->set('transportSetData.unloading_at', '2026-01-01 12:00')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('calendar-refresh')
        ->assertDispatched('transport-set-saved')
        ->assertSet('isOpen', false);

    $transportSet->refresh();
    expect($transportSet->status)->toBe(DeliveryTransportSetStatusEnum::ASSIGNED);
    expect($transportSet->driver_id)->toBe($driver->id);
});
