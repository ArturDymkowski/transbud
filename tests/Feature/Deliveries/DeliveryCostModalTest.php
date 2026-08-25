<?php

use App\Enums\CurrencyEnum;
use App\Enums\DeliveryCostTypeEnum;
use App\Livewire\Modals\DeliveryCostModal;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Delivery;
use App\Models\DeliveryCost;
use App\Models\DeliveryTransportSet;
use Livewire\Livewire;

beforeEach(function () {
    actingAsAdmin();
});

function createProfitabilityDeliveryForModal(?int $freightAmountGrosze = 1_500_000): Delivery
{
    $contractor = Contractor::factory()->create();
    $address = ContractorAddress::factory()->create(['contractor_id' => $contractor->id]);

    return Delivery::factory()->create([
        'contractor_id' => $contractor->id,
        'contractor_address_id' => $address->id,
        'freight_amount' => $freightAmountGrosze,
        'currency' => CurrencyEnum::PLN->value,
    ]);
}

test('costData.amount already exists on mount, before the modal is ever opened', function () {
    // Regression guard: the amount field entangles 'costData.amount' via Alpine.
    // If that key is still missing the first time the component renders, the
    // entangled binding's write-back to the server never gets wired up correctly,
    // and the field looks empty to validation no matter what gets typed into it.
    $delivery = createProfitabilityDeliveryForModal();

    Livewire::test(DeliveryCostModal::class, ['delivery' => $delivery])
        ->assertSet('costData.amount', '');
});

test('the open-create-cost-modal event opens the modal for a new cost', function () {
    $delivery = createProfitabilityDeliveryForModal();

    Livewire::test(DeliveryCostModal::class, ['delivery' => $delivery])
        ->dispatch('open-create-cost-modal')
        ->assertSet('isOpen', true)
        ->assertSet('editingCostId', null);
});

test('the open-edit-cost-modal event populates the modal with the cost data', function () {
    $delivery = createProfitabilityDeliveryForModal();
    $cost = DeliveryCost::factory()->create(['delivery_id' => $delivery->id, 'amount' => 10_000]);

    Livewire::test(DeliveryCostModal::class, ['delivery' => $delivery])
        ->dispatch('open-edit-cost-modal', costId: $cost->id)
        ->assertSet('isOpen', true)
        ->assertSet('editingCostId', $cost->id)
        ->assertSet('costData.amount', '100.00');
});

test('a cost can be added directly to a delivery', function () {
    $delivery = createProfitabilityDeliveryForModal();

    Livewire::test(DeliveryCostModal::class, ['delivery' => $delivery])
        ->dispatch('open-create-cost-modal')
        ->set('costData.type', DeliveryCostTypeEnum::FUEL->value)
        ->set('costData.amount', '350.50')
        ->set('costData.description', 'Tankowanie')
        ->call('saveCost')
        ->assertHasNoErrors()
        ->assertDispatched('cost-saved')
        ->assertSet('isOpen', false);

    $cost = $delivery->costs()->sole();
    expect($cost->amount)->toBe(35050);
    expect($cost->delivery_transport_set_id)->toBeNull();
    expect($cost->currency)->toBe(CurrencyEnum::PLN);
    expect($cost->type)->toBe(DeliveryCostTypeEnum::FUEL);
});

test('a cost can be added to a specific transport set', function () {
    $delivery = createProfitabilityDeliveryForModal();
    $transportSet = DeliveryTransportSet::factory()->create(['delivery_id' => $delivery->id]);

    Livewire::test(DeliveryCostModal::class, ['delivery' => $delivery])
        ->dispatch('open-create-cost-modal', transportSetId: $transportSet->id)
        ->set('costData.type', DeliveryCostTypeEnum::TOLL->value)
        ->set('costData.amount', '80')
        ->call('saveCost')
        ->assertHasNoErrors();

    $cost = $delivery->costs()->sole();
    expect($cost->delivery_transport_set_id)->toBe($transportSet->id);
    expect($cost->amount)->toBe(8000);
});

test('a comma decimal separator is accepted (pl locale convention)', function () {
    $delivery = createProfitabilityDeliveryForModal();

    Livewire::test(DeliveryCostModal::class, ['delivery' => $delivery])
        ->dispatch('open-create-cost-modal')
        ->set('costData.type', DeliveryCostTypeEnum::FUEL->value)
        ->set('costData.amount', '1500,50')
        ->call('saveCost')
        ->assertHasNoErrors();

    expect($delivery->costs()->sole()->amount)->toBe(150050);
});

test('an existing cost can be edited', function () {
    $delivery = createProfitabilityDeliveryForModal();
    $cost = DeliveryCost::factory()->create(['delivery_id' => $delivery->id, 'amount' => 10_000]);

    Livewire::test(DeliveryCostModal::class, ['delivery' => $delivery])
        ->dispatch('open-edit-cost-modal', costId: $cost->id)
        ->set('costData.amount', '200.00')
        ->call('saveCost')
        ->assertHasNoErrors();

    expect($cost->fresh()->amount)->toBe(20_000);
});

test('a cost cannot be assigned to a transport set belonging to another delivery', function () {
    $delivery = createProfitabilityDeliveryForModal();
    $otherDelivery = createProfitabilityDeliveryForModal();
    $foreignTransportSet = DeliveryTransportSet::factory()->create(['delivery_id' => $otherDelivery->id]);

    Livewire::test(DeliveryCostModal::class, ['delivery' => $delivery])
        ->dispatch('open-create-cost-modal')
        ->set('costData.type', DeliveryCostTypeEnum::FUEL->value)
        ->set('costData.amount', '100')
        ->set('costData.delivery_transport_set_id', $foreignTransportSet->id)
        ->call('saveCost')
        ->assertHasErrors('costData.delivery_transport_set_id');

    expect(DeliveryCost::count())->toBe(0);
});

test('a saved cost always inherits the delivery currency, not a user-supplied one', function () {
    $delivery = createProfitabilityDeliveryForModal(1_500_000);
    $delivery->update(['currency' => CurrencyEnum::EUR->value]);

    Livewire::test(DeliveryCostModal::class, ['delivery' => $delivery])
        ->dispatch('open-create-cost-modal')
        ->set('costData.type', DeliveryCostTypeEnum::FUEL->value)
        ->set('costData.amount', '100')
        ->call('saveCost')
        ->assertHasNoErrors();

    expect($delivery->costs()->sole()->currency)->toBe(CurrencyEnum::EUR);
});
