<?php

use App\Models\Delivery;

function deliveryWithCostsSum(?int $freightAmount, int $costsSum): Delivery
{
    $delivery = new Delivery(['freight_amount' => $freightAmount]);
    $delivery->costs_sum_amount = $costsSum;

    return $delivery;
}

test('total cost amount uses the preloaded costs sum instead of querying', function () {
    $delivery = deliveryWithCostsSum(500000, 120000);

    expect($delivery->totalCostAmount())->toBe(120000);
});

test('profit amount is revenue minus total cost', function () {
    $delivery = deliveryWithCostsSum(500000, 120000);

    expect($delivery->profitAmount())->toBe(380000);
});

test('profit amount can be negative when costs exceed revenue', function () {
    $delivery = deliveryWithCostsSum(100000, 150000);

    expect($delivery->profitAmount())->toBe(-50000);
});

test('an empty freight amount is treated as zero revenue', function () {
    $delivery = deliveryWithCostsSum(null, 50000);

    expect($delivery->profitAmount())->toBe(-50000);
});

test('margin percent is profit as a percentage of revenue, rounded to 2 decimals', function () {
    $delivery = deliveryWithCostsSum(300000, 100000);

    expect($delivery->marginPercent())->toBe(66.67);
});

test('margin percent is null when revenue is zero', function () {
    $delivery = deliveryWithCostsSum(0, 0);

    expect($delivery->marginPercent())->toBeNull();
});

test('margin percent is null when the freight amount is empty', function () {
    $delivery = deliveryWithCostsSum(null, 0);

    expect($delivery->marginPercent())->toBeNull();
});
