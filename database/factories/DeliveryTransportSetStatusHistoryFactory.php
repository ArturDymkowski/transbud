<?php

namespace Database\Factories;

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Models\DeliveryTransportSet;
use App\Models\DeliveryTransportSetStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryTransportSetStatusHistory>
 */
class DeliveryTransportSetStatusHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'delivery_transport_set_id' => DeliveryTransportSet::factory(),
            'status' => $this->faker->numberBetween(0, count(DeliveryTransportSetStatusEnum::cases()) - 1),
            'changed_by' => User::factory(),
        ];
    }
}
