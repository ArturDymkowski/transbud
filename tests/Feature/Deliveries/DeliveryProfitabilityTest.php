<?php

use App\Enums\CurrencyEnum;
use App\Enums\DeliveryCostTypeEnum;
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

test('a cost can be added directly to a delivery', function () {
    $delivery = createProfitabilityDelivery();

    Livewire::test(DeliveryProfitabilityPanel::class, ['delivery' => $delivery])
        ->call('openCreateCostModal')
        ->set('costData.type', DeliveryCostTypeEnum::FUEL->value)
        ->set('costData.amount', '350.50')
        ->set('costData.description', 'Tankowanie')
        ->call('saveCost')
        ->assertHasNoErrors();

    $cost = $delivery->costs()->sole();
    expect($cost->amount)->toBe(35050);
    expect($cost->delivery_transport_set_id)->toBeNull();
    expect($cost->currency)->toBe(CurrencyEnum::PLN);
    expect($cost->type)->toBe(DeliveryCostTypeEnum::FUEL);
});

test('a cost can be added to a specific transport set', function () {
    $delivery = createProfitabilityDelivery();
    $transportSet = DeliveryTransportSet::factory()->create(['delivery_id' => $delivery->id]);

    Livewire::test(DeliveryProfitabilityPanel::class, ['delivery' => $delivery])
        ->call('openCreateCostModal', $transportSet->id)
        ->set('costData.type', DeliveryCostTypeEnum::TOLL->value)
        ->set('costData.amount', '80')
        ->call('saveCost')
        ->assertHasNoErrors();

    $cost = $delivery->costs()->sole();
    expect($cost->delivery_transport_set_id)->toBe($transportSet->id);
    expect($cost->amount)->toBe(8000);
});

test('an existing cost can be edited', function () {
    $delivery = createProfitabilityDelivery();
    $cost = DeliveryCost::factory()->create(['delivery_id' => $delivery->id, 'amount' => 10_000]);

    Livewire::test(DeliveryProfitabilityPanel::class, ['delivery' => $delivery])
        ->call('openEditCostModal', $cost->id)
        ->set('costData.amount', '200.00')
        ->call('saveCost')
        ->assertHasNoErrors();

    expect($cost->fresh()->amount)->toBe(20_000);
});

test('a cost can be deleted', function () {
    $delivery = createProfitabilityDelivery();
    $cost = DeliveryCost::factory()->create(['delivery_id' => $delivery->id]);

    Livewire::test(DeliveryProfitabilityPanel::class, ['delivery' => $delivery])
        ->call('deleteCost', $cost->id);

    expect(DeliveryCost::find($cost->id))->toBeNull();
});

test('a cost cannot be assigned to a transport set belonging to another delivery', function () {
    $delivery = createProfitabilityDelivery();
    $otherDelivery = createProfitabilityDelivery();
    $foreignTransportSet = DeliveryTransportSet::factory()->create(['delivery_id' => $otherDelivery->id]);

    Livewire::test(DeliveryProfitabilityPanel::class, ['delivery' => $delivery])
        ->call('openCreateCostModal')
        ->set('costData.type', DeliveryCostTypeEnum::FUEL->value)
        ->set('costData.amount', '100')
        ->set('costData.delivery_transport_set_id', $foreignTransportSet->id)
        ->call('saveCost')
        ->assertHasErrors('costData.delivery_transport_set_id');

    expect(DeliveryCost::count())->toBe(0);
});

test('a saved cost always inherits the delivery currency, not a user-supplied one', function () {
    $delivery = createProfitabilityDelivery(1_500_000);
    $delivery->update(['currency' => CurrencyEnum::EUR->value]);

    Livewire::test(DeliveryProfitabilityPanel::class, ['delivery' => $delivery])
        ->call('openCreateCostModal')
        ->set('costData.type', DeliveryCostTypeEnum::FUEL->value)
        ->set('costData.amount', '100')
        ->call('saveCost')
        ->assertHasNoErrors();

    expect($delivery->costs()->sole()->currency)->toBe(CurrencyEnum::EUR);
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
