<?php

namespace Database\Factories;

use App\Enums\CurrencyEnum;
use App\Enums\DeliveryCostTypeEnum;
use App\Models\Delivery;
use App\Models\DeliveryCost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryCost>
 */
class DeliveryCostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'delivery_id' => Delivery::factory(),
            'delivery_transport_set_id' => null,
            'type' => $this->faker->numberBetween(0, count(DeliveryCostTypeEnum::cases()) - 1),
            'amount' => $this->faker->numberBetween(1000, 500000),
            'currency' => CurrencyEnum::PLN->value,
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
