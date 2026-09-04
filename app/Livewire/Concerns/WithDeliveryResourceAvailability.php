<?php

namespace App\Livewire\Concerns;

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Models\DeliveryTransportSet;
use Closure;

trait WithDeliveryResourceAvailability
{
    private function validateResourceAvailability(
        Closure $fail,
        string $column,
        mixed $value,
        ?string $loadingAt,
        ?string $unloadingAt,
        ?int $excludeDeliveryId,
        string $busyMessage,
    ): void {
        if (! $value || ! $loadingAt || ! $unloadingAt) {
            return;
        }

        $conflict = DeliveryTransportSet::query()
            ->where($column, $value)
            ->whereNotIn('status', [
                DeliveryTransportSetStatusEnum::DRAFT->value,
                DeliveryTransportSetStatusEnum::CANCELLED->value,
            ])
            ->when($excludeDeliveryId, fn ($query) => $query->where('delivery_id', '!=', $excludeDeliveryId))
            ->where('loading_at', '<', $unloadingAt)
            ->where('unloading_at', '>', $loadingAt)
            ->exists();

        if ($conflict) {
            $fail($busyMessage);
        }
    }
}
