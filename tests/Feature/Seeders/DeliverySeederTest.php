<?php

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Models\DeliveryTransportSet;
use Illuminate\Support\Facades\Artisan;

test('seeded transport sets never double-book a driver, tractor or trailer across different deliveries', function () {
    Artisan::call('db:seed', ['--force' => true]);

    $activeSets = DeliveryTransportSet::whereNotIn('status', [
        DeliveryTransportSetStatusEnum::DRAFT->value,
        DeliveryTransportSetStatusEnum::CANCELLED->value,
    ])->get(['id', 'delivery_id', 'driver_id', 'vehicle_id', 'trailer_id', 'loading_at', 'unloading_at']);

    expect($activeSets)->not->toBeEmpty();

    foreach (['driver_id', 'vehicle_id', 'trailer_id'] as $column) {
        foreach ($activeSets as $a) {
            foreach ($activeSets as $b) {
                if ($a->id >= $b->id || $a->delivery_id === $b->delivery_id) {
                    continue;
                }

                if ($a->{$column} !== $b->{$column}) {
                    continue;
                }

                $overlaps = $a->loading_at < $b->unloading_at && $a->unloading_at > $b->loading_at;

                expect($overlaps)->toBeFalse(
                    "Resource {$column}={$a->{$column}} double-booked between delivery {$a->delivery_id} (set {$a->id}) and delivery {$b->delivery_id} (set {$b->id})."
                );
            }
        }
    }
});
