<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $expiryDate = fn () => $this->faker->boolean(50)
            ? (

            $this->faker->boolean(80)
                ? $this->faker->dateTimeBetween('now', '+3 years')->format('Y-m-d')
                : $this->faker->dateTimeBetween('-3 years', 'yesterday')->format('Y-m-d')
            )
            : null;

        return [
            'registration_number' => strtoupper($this->faker->bothify('?? #####')),
            'vin' => strtoupper($this->faker->bothify('#################')),
            'type' => $this->faker->numberBetween(0,1),

            'technical_inspection_expiry_date' => $expiryDate(),
            'insurance_expiry_date' => $expiryDate(),
            'tachograph_inspection_expiry_date' => $expiryDate(),

            'additional_notes' => $this->faker->boolean(30)
                ? $this->faker->sentence()
                : null,

            'is_active' => $this->faker->boolean(90),
        ];
    }
}
