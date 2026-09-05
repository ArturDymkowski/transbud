<?php

use App\Enums\DeliveryStatusEnum;
use App\Enums\DeliveryTransportSetStatusEnum;
use App\Livewire\Concerns\WithDeliveryStatusComputation;

function statusComputer(): object
{
    return new class
    {
        use WithDeliveryStatusComputation;

        public function compute(iterable $transportSetStatuses): DeliveryStatusEnum
        {
            return $this->computeDeliveryStatus($transportSetStatuses);
        }
    };
}

test('no transport sets means the delivery is planned', function () {
    expect(statusComputer()->compute([]))->toBe(DeliveryStatusEnum::PLANNED);
});

test('only draft transport sets means the delivery is planned', function () {
    $statuses = [DeliveryTransportSetStatusEnum::DRAFT, DeliveryTransportSetStatusEnum::DRAFT];

    expect(statusComputer()->compute($statuses))->toBe(DeliveryStatusEnum::PLANNED);
});

test('draft transport sets are ignored when computing the status', function () {
    $statuses = [DeliveryTransportSetStatusEnum::DRAFT, DeliveryTransportSetStatusEnum::ASSIGNED];

    expect(statusComputer()->compute($statuses))->toBe(DeliveryStatusEnum::ASSIGNED);
});

test('all transport sets assigned means the delivery is assigned', function () {
    $statuses = [DeliveryTransportSetStatusEnum::ASSIGNED, DeliveryTransportSetStatusEnum::ASSIGNED];

    expect(statusComputer()->compute($statuses))->toBe(DeliveryStatusEnum::ASSIGNED);
});

test('all transport sets completed means the delivery is completed', function () {
    $statuses = [DeliveryTransportSetStatusEnum::COMPLETED, DeliveryTransportSetStatusEnum::COMPLETED];

    expect(statusComputer()->compute($statuses))->toBe(DeliveryStatusEnum::COMPLETED);
});

test('a mix of assigned and completed transport sets means the delivery is in progress', function () {
    $statuses = [DeliveryTransportSetStatusEnum::ASSIGNED, DeliveryTransportSetStatusEnum::COMPLETED];

    expect(statusComputer()->compute($statuses))->toBe(DeliveryStatusEnum::IN_PROGRESS);
});

test('a mix of assigned and loading/unloading/in-transit transport sets means the delivery is in progress', function (DeliveryTransportSetStatusEnum $status) {
    $statuses = [DeliveryTransportSetStatusEnum::ASSIGNED, $status];

    expect(statusComputer()->compute($statuses))->toBe(DeliveryStatusEnum::IN_PROGRESS);
})->with([
    DeliveryTransportSetStatusEnum::LOADING,
    DeliveryTransportSetStatusEnum::UNLOADING,
    DeliveryTransportSetStatusEnum::IN_TRANSIT,
]);

test('raw integer status values are accepted the same way as enum instances', function () {
    $statuses = [DeliveryTransportSetStatusEnum::ASSIGNED->value, DeliveryTransportSetStatusEnum::COMPLETED->value];

    expect(statusComputer()->compute($statuses))->toBe(DeliveryStatusEnum::IN_PROGRESS);
});

test('all transport sets cancelled means the delivery is cancelled', function () {
    $statuses = [DeliveryTransportSetStatusEnum::CANCELLED, DeliveryTransportSetStatusEnum::CANCELLED];

    expect(statusComputer()->compute($statuses))->toBe(DeliveryStatusEnum::CANCELLED);
});

test('a mix of cancelled and assigned transport sets means the delivery is in progress', function () {
    $statuses = [DeliveryTransportSetStatusEnum::CANCELLED, DeliveryTransportSetStatusEnum::ASSIGNED];

    expect(statusComputer()->compute($statuses))->toBe(DeliveryStatusEnum::IN_PROGRESS);
});

test('a mix of cancelled and completed transport sets means the delivery is in progress', function () {
    $statuses = [DeliveryTransportSetStatusEnum::CANCELLED, DeliveryTransportSetStatusEnum::COMPLETED];

    expect(statusComputer()->compute($statuses))->toBe(DeliveryStatusEnum::IN_PROGRESS);
});
