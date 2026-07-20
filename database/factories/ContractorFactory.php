<?php

namespace Database\Factories;

use App\Models\Contractor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contractor>
 */
class ContractorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'active' => fake()->boolean(90),
            'name' => fake()->company(),
            'nip' => fake()->numerify('##########'),
            'regon' => fake()->numerify('#########'),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
