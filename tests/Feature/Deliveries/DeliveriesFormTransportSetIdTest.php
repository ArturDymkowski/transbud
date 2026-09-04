<?php

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Livewire\Forms\DeliveriesForm;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Delivery;
use App\Models\DeliveryTransportSet;
use Livewire\Livewire;

beforeEach(function () {
    actingAsAdmin();
});

function createDeliveryWithMatchingAddressForTransportSetIdTest(): Delivery
{
    $contractor = Contractor::factory()->create();
    $address = ContractorAddress::factory()->create(['contractor_id' => $contractor->id]);

    return Delivery::factory()->create([
        'contractor_id' => $contractor->id,
        'contractor_address_id' => $address->id,
    ]);
}

test('saving with a transport set id belonging to another delivery fails validation instead of erroring', function () {
    $delivery = createDeliveryWithMatchingAddressForTransportSetIdTest();
    $transportSet = DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'status' => DeliveryTransportSetStatusEnum::DRAFT,
    ]);

    $otherDelivery = createDeliveryWithMatchingAddressForTransportSetIdTest();
    $foreignTransportSet = DeliveryTransportSet::factory()->create(['delivery_id' => $otherDelivery->id]);

    Livewire::test(DeliveriesForm::class, ['delivery' => $delivery])
        ->set('transportSetsData.0.id', $foreignTransportSet->id)
        ->call('save')
        ->assertHasErrors(['transportSetsData']);

    expect($transportSet->fresh())->not->toBeNull();
    expect($foreignTransportSet->fresh()->delivery_id)->toBe($otherDelivery->id);
});

test('saving with a nonexistent transport set id fails validation instead of erroring', function () {
    $delivery = createDeliveryWithMatchingAddressForTransportSetIdTest();
    DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'status' => DeliveryTransportSetStatusEnum::DRAFT,
    ]);

    Livewire::test(DeliveriesForm::class, ['delivery' => $delivery])
        ->set('transportSetsData.0.id', 999999)
        ->call('save')
        ->assertHasErrors(['transportSetsData']);
});
