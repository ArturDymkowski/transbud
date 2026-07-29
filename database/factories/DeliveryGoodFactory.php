<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\DeliveryGood;
use App\Models\Good;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryGood>
 */
class DeliveryGoodFactory extends Factory
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
            'good_id' => Good::factory(),
            'unit_id' => Unit::factory(),
            'quantity' => $this->faker->randomFloat(2, 1, 1000),
        ];
    }
}
