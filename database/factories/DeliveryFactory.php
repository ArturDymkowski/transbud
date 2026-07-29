<?php

namespace Database\Factories;

use App\Enums\DeliveryStatusEnum;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Delivery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Delivery>
 */
class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => strtoupper($this->faker->unique()->bothify('DEL-####-???')),
            'contractor_id' => Contractor::factory(),
            'contractor_address_id' => ContractorAddress::factory(),
            'loading_address' => $this->faker->address(),
            'status' => $this->faker->numberBetween(0, count(DeliveryStatusEnum::cases()) - 1),
        ];
    }
}
