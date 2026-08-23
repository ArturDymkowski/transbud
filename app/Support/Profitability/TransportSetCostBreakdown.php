<?php

namespace App\Support\Profitability;

use App\Models\DeliveryCost;
use App\Models\DeliveryTransportSet;
use Illuminate\Support\Collection;

/**
 * Costs of a single transport set, grouped for the "Koszty zestawów" section
 * of the delivery profitability view.
 */
final readonly class TransportSetCostBreakdown
{
    /**
     * @param  Collection<int, DeliveryCost>  $costs
     */
    private function __construct(
        public DeliveryTransportSet $transportSet,
        public Collection $costs,
        public int $totalAmount,
    ) {}

    /**
     * @param  Collection<int, DeliveryCost>  $costs
     */
    public static function forTransportSet(DeliveryTransportSet $transportSet, Collection $costs): self
    {
        return new self($transportSet, $costs, (int) $costs->sum('amount'));
    }

    /**
     * Driver + vehicle + trailer label, falling back to "#id" when none are assigned yet.
     */
    public function label(): string
    {
        $parts = collect([
            $this->transportSet->driver->name ?? null,
            $this->transportSet->vehicle->registration_number ?? null,
            $this->transportSet->trailer->registration_number ?? null,
        ])->filter();

        return $parts->isNotEmpty() ? $parts->implode(' / ') : ('#'.$this->transportSet->id);
    }
}
