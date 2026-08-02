<?php

namespace App\Livewire\Concerns;

use App\Enums\DeliveryStatusEnum;
use App\Enums\DeliveryTransportSetStatusEnum;

trait WithDeliveryStatusComputation
{
    private function computeDeliveryStatus(iterable $transportSetStatuses): DeliveryStatusEnum
    {
        $statuses = collect($transportSetStatuses)
            ->map(fn ($status) => $status instanceof DeliveryTransportSetStatusEnum ? $status->value : (int) $status)
            ->reject(fn (int $status) => $status === DeliveryTransportSetStatusEnum::DRAFT->value);

        if ($statuses->isEmpty()) {
            return DeliveryStatusEnum::PLANNED;
        }

        if ($statuses->every(fn (int $status) => $status === DeliveryTransportSetStatusEnum::COMPLETED->value)) {
            return DeliveryStatusEnum::COMPLETED;
        }

        if ($statuses->contains(fn (int $status) => $status !== DeliveryTransportSetStatusEnum::ASSIGNED->value)) {
            return DeliveryStatusEnum::IN_PROGRESS;
        }

        return DeliveryStatusEnum::ASSIGNED;
    }
}
