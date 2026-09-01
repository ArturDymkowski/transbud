<?php

namespace Database\Factories;

use App\Models\LoginAuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoginAuditLog>
 */
class LoginAuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->safeEmail(),
            'successful' => true,
            'ip' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'session_id' => $this->faker->uuid(),
            'logout_at' => null,
            'created_at' => now(),
        ];
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'successful' => false,
            'session_id' => null,
        ]);
    }

    public function loggedOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'logout_at' => $this->faker->dateTimeBetween($attributes['created_at'] ?? '-1 hour'),
        ]);
    }
}
