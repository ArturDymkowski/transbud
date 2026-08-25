<?php

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Delivery;
use App\Models\DeliveryTransportSet;

beforeEach(function () {
    $this->admin = actingAsAdmin();
});

test('the transport set status is rendered as a colored badge, not plain text', function () {
    $contractor = Contractor::factory()->create();
    $address = ContractorAddress::factory()->create(['contractor_id' => $contractor->id]);
    $delivery = Delivery::factory()->create([
        'contractor_id' => $contractor->id,
        'contractor_address_id' => $address->id,
    ]);
    $transportSet = DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
    ]);

    $this->get(route('deliveries.show', $delivery))
        ->assertOk()
        ->assertSee($transportSet->status->color(), false);
});
