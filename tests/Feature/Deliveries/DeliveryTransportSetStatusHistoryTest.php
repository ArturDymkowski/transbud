<?php

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Livewire\Forms\DeliveriesForm;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Delivery;
use App\Models\DeliveryTransportSet;
use App\Models\DeliveryTransportSetStatusHistory;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = actingAsAdmin();
});

function createDeliveryWithMatchingAddress(): Delivery
{
    $contractor = Contractor::factory()->create();
    $address = ContractorAddress::factory()->create(['contractor_id' => $contractor->id]);

    return Delivery::factory()->create([
        'contractor_id' => $contractor->id,
        'contractor_address_id' => $address->id,
    ]);
}

test('creating a transport set records its initial status in the history', function () {
    $delivery = createDeliveryWithMatchingAddress();

    Livewire::test(DeliveriesForm::class, ['delivery' => $delivery])
        ->call('addTransportSetRow')
        ->set('transportSetsData.0.status', DeliveryTransportSetStatusEnum::DRAFT->value)
        ->call('save')
        ->assertHasNoErrors();

    $transportSet = $delivery->fresh()->transportSets()->first();

    expect($transportSet->statusHistories)->toHaveCount(1);
    expect($transportSet->statusHistories->first()->status)->toBe(DeliveryTransportSetStatusEnum::DRAFT);
    expect($transportSet->statusHistories->first()->changed_by)->toBe($this->admin->id);
});

test('changing a transport set status records a new history entry', function () {
    $delivery = createDeliveryWithMatchingAddress();
    $transportSet = DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'status' => DeliveryTransportSetStatusEnum::DRAFT,
    ]);

    Livewire::test(DeliveriesForm::class, ['delivery' => $delivery])
        ->set('transportSetsData.0.status', DeliveryTransportSetStatusEnum::ASSIGNED->value)
        ->call('save')
        ->assertHasNoErrors();

    $transportSet->refresh();

    expect($transportSet->status)->toBe(DeliveryTransportSetStatusEnum::ASSIGNED);
    expect($transportSet->statusHistories)->toHaveCount(1);
    expect($transportSet->statusHistories->first()->status)->toBe(DeliveryTransportSetStatusEnum::ASSIGNED);
});

test('saving without changing the status does not add a duplicate history entry', function () {
    $delivery = createDeliveryWithMatchingAddress();
    $transportSet = DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'status' => DeliveryTransportSetStatusEnum::DRAFT,
    ]);
    $transportSet->statusHistories()->create(['status' => DeliveryTransportSetStatusEnum::DRAFT->value]);

    Livewire::test(DeliveriesForm::class, ['delivery' => $delivery])
        ->call('save')
        ->assertHasNoErrors();

    expect($transportSet->fresh()->statusHistories)->toHaveCount(1);
});

test('status history is visible on the delivery show page', function () {
    $delivery = createDeliveryWithMatchingAddress();
    $transportSet = DeliveryTransportSet::factory()->create(['delivery_id' => $delivery->id]);
    DeliveryTransportSetStatusHistory::factory()->create([
        'delivery_transport_set_id' => $transportSet->id,
        'status' => $transportSet->status,
        'changed_by' => $this->admin->id,
    ]);

    $this->get(route('deliveries.show', $delivery))
        ->assertOk()
        ->assertSee(trans('deliveries.status_history.tab'))
        ->assertSee($this->admin->name);
});
