<?php

namespace Database\Factories;

use App\Enums\CurrencyEnum;
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
            'freight_amount' => null,
            'currency' => CurrencyEnum::PLN->value,
        ];
    }

    /**
     * Set a freight amount (in grosze) for profitability tests.
     */
    public function withFreightAmount(int $amountGrosze, CurrencyEnum $currency = CurrencyEnum::PLN): static
    {
        return $this->state(fn () => [
            'freight_amount' => $amountGrosze,
            'currency' => $currency->value,
        ]);
    }
}
