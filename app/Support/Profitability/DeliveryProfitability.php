<?php

namespace App\Support\Profitability;

use App\Enums\CurrencyEnum;
use App\Enums\MarginRatingEnum;
use App\Models\Delivery;
use App\Models\DeliveryCost;
use App\Models\DeliveryTransportSet;
use Illuminate\Support\Collection;

/**
 * Profitability breakdown of a single delivery. Nothing here is persisted —
 * it's always computed on the fly from the delivery's freight amount and its
 * costs (both direct and per-transport-set), per Delivery::totalCostAmount()
 * / profitAmount() / marginPercent().
 */
final readonly class DeliveryProfitability
{
    /**
     * @param  Collection<int, DeliveryCost>  $directCosts
     * @param  Collection<int, TransportSetCostBreakdown>  $transportSetBreakdowns
     */
    private function __construct(
        public int $revenueAmount,
        public CurrencyEnum $currency,
        public int $totalCostAmount,
        public int $profitAmount,
        public ?float $marginPercent,
        public Collection $directCosts,
        public Collection $transportSetBreakdowns,
    ) {}

    public static function fromDelivery(Delivery $delivery): self
    {
        $delivery->loadMissing([
            'costs.transportSet.driver',
            'costs.transportSet.vehicle',
            'costs.transportSet.trailer',
            'transportSets',
        ]);

        $allCosts = $delivery->costs;
        $directCosts = $allCosts->whereNull('delivery_transport_set_id')->values();

        $transportSetBreakdowns = $delivery->transportSets->map(
            fn (DeliveryTransportSet $transportSet) => TransportSetCostBreakdown::forTransportSet(
                $transportSet,
                $allCosts->where('delivery_transport_set_id', $transportSet->id)->values(),
            )
        );

        return new self(
            revenueAmount: $delivery->freight_amount ?? 0,
            currency: $delivery->currency,
            totalCostAmount: $delivery->totalCostAmount(),
            profitAmount: $delivery->profitAmount(),
            marginPercent: $delivery->marginPercent(),
            directCosts: $directCosts,
            transportSetBreakdowns: $transportSetBreakdowns,
        );
    }

    /**
     * Hex color for the margin badge, driven by config('profitability.margin_thresholds').
     */
    public function marginColor(): string
    {
        return MarginRatingEnum::fromPercent($this->marginPercent)->color();
    }
}
