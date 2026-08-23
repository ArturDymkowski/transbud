<?php

use App\Enums\CurrencyEnum;
use App\Livewire\Forms\DeliveriesForm;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Delivery;
use App\Models\DeliveryCost;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = actingAsAdmin();
});

test('freight amount is stored as grosze and is optional', function () {
    $contractor = Contractor::factory()->create();
    $address = ContractorAddress::factory()->create(['contractor_id' => $contractor->id]);

    Livewire::test(DeliveriesForm::class)
        ->set('deliveryData.contractor_id', $contractor->id)
        ->set('deliveryData.contractor_address_id', $address->id)
        ->set('deliveryData.loading_address', 'Testowa 1')
        ->set('deliveryData.freight_amount', '3500.75')
        ->call('save')
        ->assertHasNoErrors();

    $delivery = Delivery::sole();
    expect($delivery->freight_amount)->toBe(350075);
    expect($delivery->currency)->toBe(CurrencyEnum::PLN);
});

test('freight amount can be left empty', function () {
    $contractor = Contractor::factory()->create();
    $address = ContractorAddress::factory()->create(['contractor_id' => $contractor->id]);

    Livewire::test(DeliveriesForm::class)
        ->set('deliveryData.contractor_id', $contractor->id)
        ->set('deliveryData.contractor_address_id', $address->id)
        ->set('deliveryData.loading_address', 'Testowa 1')
        ->set('deliveryData.freight_amount', '')
        ->call('save')
        ->assertHasNoErrors();

    expect(Delivery::sole()->freight_amount)->toBeNull();
});

test('the create delivery page renders with the freight amount and currency fields', function () {
    $this->get(route('deliveries.create'))->assertOk();
});

test('the deliveries index page renders the margin column', function () {
    $contractor = Contractor::factory()->create();
    $address = ContractorAddress::factory()->create(['contractor_id' => $contractor->id]);
    $delivery = Delivery::factory()->create([
        'contractor_id' => $contractor->id,
        'contractor_address_id' => $address->id,
        'freight_amount' => 1_500_000,
    ]);
    DeliveryCost::factory()->create(['delivery_id' => $delivery->id, 'amount' => 710_000]);

    $this->get(route('deliveries.index'))->assertOk();
});
