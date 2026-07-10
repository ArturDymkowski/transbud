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
            'house_nr' => fake()->buildingNumber(),
            'apartment_nr' => fake()->optional()->buildingNumber(),

            'extra_info' => fake()->optional()->sentence(),

            'driving_license_number' => strtoupper(fake()->bothify('???######')),

            'driving_license_expiry_date' => fake()->dateTimeBetween('now', '+5 years'),

            'identity_card_number' => strtoupper(fake()->bothify('???######')),

            'identity_card_expiry_date' => fake()->dateTimeBetween('now', '+2 years'),

            'is_active' => fake()->boolean(90),
        ];
    }
}
