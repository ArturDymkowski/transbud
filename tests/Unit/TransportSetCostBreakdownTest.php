<?php

use App\Models\DeliveryCost;
use App\Models\DeliveryTransportSet;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Support\Profitability\TransportSetCostBreakdown;
use Illuminate\Support\Collection;

function transportSetCostBreakdown(array $transportSetAttributes, Collection $costs): TransportSetCostBreakdown
{
    $transportSet = new DeliveryTransportSet($transportSetAttributes);
    $transportSet->id = $transportSetAttributes['id'] ?? 1;

    if (array_key_exists('driver', $transportSetAttributes)) {
        $transportSet->setRelation('driver', $transportSetAttributes['driver']);
    }

    if (array_key_exists('vehicle', $transportSetAttributes)) {
        $transportSet->setRelation('vehicle', $transportSetAttributes['vehicle']);
    }

    if (array_key_exists('trailer', $transportSetAttributes)) {
        $transportSet->setRelation('trailer', $transportSetAttributes['trailer']);
    }

    return TransportSetCostBreakdown::forTransportSet($transportSet, $costs);
}

test('total amount sums the given costs, ignoring costs belonging to other transport sets', function () {
    $costs = collect([
        new DeliveryCost(['amount' => 10000]),
        new DeliveryCost(['amount' => 2500]),
    ]);

    $breakdown = transportSetCostBreakdown(['id' => 1], $costs);

    expect($breakdown->totalAmount)->toBe(12500);
});

test('total amount is zero when no costs are attached', function () {
    $breakdown = transportSetCostBreakdown(['id' => 1], collect());

    expect($breakdown->totalAmount)->toBe(0);
});

test('label combines driver name, vehicle and trailer registration numbers', function () {
    $breakdown = transportSetCostBreakdown([
        'id' => 1,
        'driver' => new Driver(['name' => 'Jan Kowalski']),
        'vehicle' => new Vehicle(['registration_number' => 'WA12345']),
        'trailer' => new Vehicle(['registration_number' => 'WN98765']),
    ], collect());

    expect($breakdown->label())->toBe('Jan Kowalski / WA12345 / WN98765');
});

test('label skips missing resources instead of leaving empty slots', function () {
    $breakdown = transportSetCostBreakdown([
        'id' => 1,
        'driver' => new Driver(['name' => 'Jan Kowalski']),
        'vehicle' => null,
        'trailer' => null,
    ], collect());

    expect($breakdown->label())->toBe('Jan Kowalski');
});

test('label falls back to the transport set id when no resources are assigned at all', function () {
    $breakdown = transportSetCostBreakdown([
        'id' => 42,
        'driver' => null,
        'vehicle' => null,
        'trailer' => null,
    ], collect());

    expect($breakdown->label())->toBe('#42');
});
