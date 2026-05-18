<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),

            'phone' => fake()->unique()->phoneNumber(),

            'pesel' => fake()->unique()->numerify('###########'),

            'country' => fake()->numberBetween(0, 8),
            'zipcode' => fake()->postcode(),
            'city' => fake()->city(),
            'street' => fake()->streetName(),
            'street_nr' => fake()->buildingNumber(),
            'home_nr' => fake()->optional()->buildingNumber(),

            'extra_info' => fake()->optional()->sentence(),

            'driving_license_number' => strtoupper(fake()->bothify('???######')),

            'license_expiry_date' => fake()->dateTimeBetween('now', '+5 years'),

            'medical_exam_valid_until' => fake()->optional()->dateTimeBetween('now', '+2 years'),

            'is_active' => fake()->boolean(90),
        ];
    }
}
