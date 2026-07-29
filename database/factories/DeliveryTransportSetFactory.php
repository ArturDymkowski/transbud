<?php

namespace Database\Factories;

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Enums\VehicleTypeEnum;
use App\Models\Delivery;
use App\Models\DeliveryTransportSet;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryTransportSet>
 */
class DeliveryTransportSetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $loadingAt = $this->faker->dateTimeBetween('-1 month', '+1 month');

        return [
            'delivery_id' => Delivery::factory(),
            'driver_id' => Driver::factory(),
            'vehicle_id' => Vehicle::factory()->state(['type' => VehicleTypeEnum::TRACTOR->value]),
            'trailer_id' => Vehicle::factory()->state(['type' => VehicleTypeEnum::TRAILER->value]),
            'loading_at' => $loadingAt,
            'unloading_at' => $this->faker->dateTimeBetween($loadingAt, (clone $loadingAt)->modify('+3 days')),
            'status' => $this->faker->numberBetween(0, count(DeliveryTransportSetStatusEnum::cases()) - 1),
        ];
    }
}
