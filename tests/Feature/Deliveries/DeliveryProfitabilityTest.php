<?php

use App\Enums\CurrencyEnum;
use App\Livewire\Profitability\DeliveryProfitabilityPanel;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Delivery;
use App\Models\DeliveryCost;
use App\Models\DeliveryTransportSet;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = actingAsAdmin();
});

function createProfitabilityDelivery(?int $freightAmountGrosze = 1_500_000): Delivery
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

// --- Model calculations ---------------------------------------------------

test('a delivery without costs has zero total cost and profit equal to revenue', function () {
    $delivery = createProfitabilityDelivery(1_500_000);

    expect($delivery->totalCostAmount())->toBe(0);
    expect($delivery->profitAmount())->toBe(1_500_000);
    expect($delivery->marginPercent())->toBe(100.0);
});

test('a delivery with a single direct cost calculates the total correctly', function () {
    $delivery = createProfitabilityDelivery(1_500_000);
    DeliveryCost::factory()->create(['delivery_id' => $delivery->id, 'amount' => 100_000]);

    expect($delivery->totalCostAmount())->toBe(100_000);
    expect($delivery->profitAmount())->toBe(1_400_000);
});

test('a delivery with multiple costs sums all of them', function () {
    $delivery = createProfitabilityDelivery(1_500_000);
    DeliveryCost::factory()->create(['delivery_id' => $delivery->id, 'amount' => 350_000]);
    DeliveryCost::factory()->create(['delivery_id' => $delivery->id, 'amount' => 250_000]);
    DeliveryCost::factory()->create(['delivery_id' => $delivery->id, 'amount' => 80_000]);

    expect($delivery->totalCostAmount())->toBe(680_000);
});

test('costs assigned to a transport set are included in the delivery total but kept separate from direct costs', function () {
    $delivery = createProfitabilityDelivery(1_500_000);
    $transportSet = DeliveryTransportSet::factory()->create(['delivery_id' => $delivery->id]);

    DeliveryCost::factory()->create([
        'delivery_id' => $delivery->id,
        'delivery_transport_set_id' => $transportSet->id,
        'amount' => 150_000,
    ]);
    DeliveryCost::factory()->create([
        'delivery_id' => $delivery->id,
        'delivery_transport_set_id' => null,
        'amount' => 10_000,
    ]);

    expect($delivery->totalCostAmount())->toBe(160_000);
    expect($delivery->directCosts()->sum('amount'))->toBe(10_000);
    expect($transportSet->costs()->sum('amount'))->toBe(150_000);
});

test('profit and margin match the worked example from the spec', function () {
    $delivery = createProfitabilityDelivery(1_500_000);
    DeliveryCost::factory()->create(['delivery_id' => $delivery->id, 'amount' => 710_000]);

    expect($delivery->profitAmount())->toBe(790_000);
    expect($delivery->marginPercent())->toBe(52.67);
});

test('profit can be negative when costs exceed revenue', function () {
    $delivery = createProfitabilityDelivery(100_000);
    DeliveryCost::factory()->create(['delivery_id' => $delivery->id, 'amount' => 150_000]);

    expect($delivery->profitAmount())->toBe(-50_000);
    expect($delivery->marginPercent())->toBe(-50.0);
});

test('margin is null when revenue is zero, to avoid dividing by zero', function () {
    $delivery = createProfitabilityDelivery(0);
    DeliveryCost::factory()->create(['delivery_id' => $delivery->id, 'amount' => 10_000]);

    expect($delivery->marginPercent())->toBeNull();
});

test('margin is null and cost is zero-revenue when freight amount is not set', function () {
    $delivery = createProfitabilityDelivery(null);

    expect($delivery->marginPercent())->toBeNull();
    expect($delivery->profitAmount())->toBe(0 - $delivery->totalCostAmount());
});

// --- Livewire panel ---------------------------------------------------------
// The cost add/edit form itself lives in DeliveryCostModal (see
// DeliveryCostModalTest); the panel only relays the open events to it and
// reloads its own data when the modal reports back that a cost was saved.

test('opening the create/edit cost modal relays an event instead of mutating local state', function () {
    $delivery = createProfitabilityDelivery();
    $cost = DeliveryCost::factory()->create(['delivery_id' => $delivery->id]);

    Livewire::test(DeliveryProfitabilityPanel::class, ['delivery' => $delivery])
        ->call('openCreateCostModal', $cost->delivery_transport_set_id)
        ->assertDispatched('open-create-cost-modal')
        ->call('openEditCostModal', $cost->id)
        ->assertDispatched('open-edit-cost-modal');
});

test('the cost-saved event reloads the delivery so totals reflect the new cost', function () {
    $delivery = createProfitabilityDelivery(1_500_000);

    $component = Livewire::test(DeliveryProfitabilityPanel::class, ['delivery' => $delivery]);
    expect($component->instance()->profitability()->totalCostAmount)->toBe(0);

    DeliveryCost::factory()->create(['delivery_id' => $delivery->id, 'amount' => 100_000]);
    $component->dispatch('cost-saved');

    expect($component->instance()->profitability()->totalCostAmount)->toBe(100_000);
});

test('a cost can be deleted', function () {
    $delivery = createProfitabilityDelivery();
    $cost = DeliveryCost::factory()->create(['delivery_id' => $delivery->id]);

    Livewire::test(DeliveryProfitabilityPanel::class, ['delivery' => $delivery])
        ->call('deleteCost', $cost->id);

    expect(DeliveryCost::find($cost->id))->toBeNull();
});

test('the profitability tab is visible on the delivery show page', function () {
    $delivery = createProfitabilityDelivery();

    $this->get(route('deliveries.show', $delivery))
        ->assertOk()
        ->assertSee(trans('deliveries.profitability.tab'));
});

test('the show page renders the full breakdown for direct and per-transport-set costs', function () {
    $delivery = createProfitabilityDelivery(1_500_000);
    $transportSet = DeliveryTransportSet::factory()->create(['delivery_id' => $delivery->id]);

    DeliveryCost::factory()->create([
        'delivery_id' => $delivery->id,
        'delivery_transport_set_id' => $transportSet->id,
        'amount' => 150_000,
    ]);
    DeliveryCost::factory()->create([
        'delivery_id' => $delivery->id,
        'delivery_transport_set_id' => null,
        'amount' => 10_000,
    ]);

    // (1_500_000 - 160_000) / 1_500_000 * 100
    $this->get(route('deliveries.show', $delivery))
        ->assertOk()
        ->assertSee('89,33', false);
});
