<?php

namespace Database\Seeders;

use App\Enums\CurrencyEnum;
use App\Enums\DeliveryCostTypeEnum;
use App\Enums\DeliveryTransportSetStatusEnum;
use App\Enums\VehicleTypeEnum;
use App\Livewire\Concerns\WithDeliveryStatusComputation;
use App\Models\Contractor;
use App\Models\Delivery;
use App\Models\DeliveryTransportSet;
use App\Models\Driver;
use App\Models\Good;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * A dozen or so deliveries in the current month and the previous one (always relative to
 * "now", so re-running this seeder in 6 months lands the deliveries 6 months from now, not
 * on a fixed date), each with a varying number of transport sets carrying different goods,
 * plus a handful of random costs - enough for the list/calendar/profitability views to have
 * realistic data to show.
 */
class DeliverySeeder extends Seeder
{
    use WithDeliveryStatusComputation;

    /** Deliveries created per month (current + previous), a random count in this range each. */
    private const DELIVERIES_PER_MONTH = [12, 16];

    private const TRANSPORT_SETS_PER_DELIVERY = [1, 3];

    private const GOODS_PER_TRANSPORT_SET = [1, 3];

    private const COSTS_PER_DELIVERY = [1, 4];

    private const MAX_SLOT_ATTEMPTS = 50;

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('delivery_transport_set_status_history')->truncate();
        DB::table('delivery_costs')->truncate();
        DB::table('delivery_goods')->truncate();
        DB::table('delivery_transport_sets')->truncate();
        DB::table('deliveries')->truncate();
        Schema::enableForeignKeyConstraints();

        $contractors = Contractor::with('addresses')->get();
        $drivers = Driver::all();
        $tractors = Vehicle::where('type', VehicleTypeEnum::TRACTOR->value)->get();
        $trailers = Vehicle::where('type', VehicleTypeEnum::TRAILER->value)->get();
        $goods = Good::with(['units', 'defaultUnit'])->get();
        $admin = User::first();

        // startOfMonth() always lands on day 1, so subtracting a month from it can never
        // overflow into the wrong month (unlike subtracting a month from e.g. Mar 31).
        $currentMonthStart = now()->startOfMonth();
        $previousMonthStart = $currentMonthStart->copy()->subMonth();

        foreach ([$currentMonthStart, $previousMonthStart] as $monthStart) {
            $this->seedMonth($monthStart, $contractors, $drivers, $tractors, $trailers, $goods, $admin);
        }
    }

    /**
     * @param  Collection<int, Contractor>  $contractors
     * @param  Collection<int, Driver>  $drivers
     * @param  Collection<int, Vehicle>  $tractors
     * @param  Collection<int, Vehicle>  $trailers
     * @param  Collection<int, Good>  $goods
     */
    private function seedMonth(
        Carbon $monthStart,
        Collection $contractors,
        Collection $drivers,
        Collection $tractors,
        Collection $trailers,
        Collection $goods,
        ?User $admin,
    ): void {
        $monthEnd = $monthStart->copy()->endOfMonth();
        $deliveriesCount = fake()->numberBetween(...self::DELIVERIES_PER_MONTH);

        for ($i = 0; $i < $deliveriesCount; $i++) {
            $contractor = $contractors->random();

            $delivery = Delivery::factory()->create([
                'contractor_id' => $contractor->id,
                'contractor_address_id' => $contractor->addresses->random()->id,
                'freight_amount' => fake()->numberBetween(150000, 800000), // 1500-8000 PLN
            ]);

            $transportSetsCount = fake()->numberBetween(...self::TRANSPORT_SETS_PER_DELIVERY);
            $transportSets = Collection::times(
                $transportSetsCount,
                fn () => $this->createTransportSet($delivery, $monthStart, $monthEnd, $drivers, $tractors, $trailers, $goods, $admin)
            );

            $this->createCosts($delivery, $transportSets);

            $delivery->update(['status' => $this->computeDeliveryStatus($transportSets->pluck('status'))->value]);
        }
    }

    /**
     * @param  Collection<int, Driver>  $drivers
     * @param  Collection<int, Vehicle>  $tractors
     * @param  Collection<int, Vehicle>  $trailers
     * @param  Collection<int, Good>  $goods
     */
    private function createTransportSet(
        Delivery $delivery,
        Carbon $monthStart,
        Carbon $monthEnd,
        Collection $drivers,
        Collection $tractors,
        Collection $trailers,
        Collection $goods,
        ?User $admin,
    ): DeliveryTransportSet {
        [$loadingAt, $unloadingAt, $driver, $tractor, $trailer] = $this->pickAvailableSlot(
            $monthStart, $monthEnd, $drivers, $tractors, $trailers
        );
        $status = $this->transportSetStatusFor($loadingAt, $unloadingAt);

        $transportSet = $delivery->transportSets()->create([
            'driver_id' => $driver->id,
            'vehicle_id' => $tractor->id,
            'trailer_id' => $trailer->id,
            'loading_at' => $loadingAt,
            'unloading_at' => $unloadingAt,
            'status' => $status->value,
        ]);

        $transportSet->statusHistories()->create([
            'status' => $status->value,
            'changed_by' => $admin?->id,
        ]);

        $this->attachGoods($transportSet, $goods);

        return $transportSet;
    }

    /**
     * Redraws a loading/unloading window (up to MAX_SLOT_ATTEMPTS times) until a driver,
     * tractor and trailer are all simultaneously free for it - mirrors the same
     * busy/overlap rule as WithDeliveryResourceAvailability, so seeded data never violates
     * the very validation the app enforces on save.
     *
     * @param  Collection<int, Driver>  $drivers
     * @param  Collection<int, Vehicle>  $tractors
     * @param  Collection<int, Vehicle>  $trailers
     * @return array{0: Carbon, 1: Carbon, 2: Driver, 3: Vehicle, 4: Vehicle}
     */
    private function pickAvailableSlot(
        Carbon $monthStart,
        Carbon $monthEnd,
        Collection $drivers,
        Collection $tractors,
        Collection $trailers,
    ): array {
        for ($attempt = 0; $attempt < self::MAX_SLOT_ATTEMPTS; $attempt++) {
            $loadingAt = $this->roundToHalfHour(fake()->dateTimeBetween($monthStart, $monthEnd));
            // 4-72h duration in 30-minute steps, so unloading also lands on a full/half hour.
            $unloadingAt = $loadingAt->copy()->addMinutes(fake()->numberBetween(8, 144) * 30);

            $driver = $this->availableResource($drivers, 'driver_id', $loadingAt, $unloadingAt);
            $tractor = $this->availableResource($tractors, 'vehicle_id', $loadingAt, $unloadingAt);
            $trailer = $this->availableResource($trailers, 'trailer_id', $loadingAt, $unloadingAt);

            if ($driver && $tractor && $trailer) {
                return [$loadingAt, $unloadingAt, $driver, $tractor, $trailer];
            }
        }

        throw new RuntimeException(
            'DeliverySeeder: nie udało się znaleźć wolnego terminu dla zestawu transportowego po '.
            self::MAX_SLOT_ATTEMPTS.' próbach - flota (kierowcy/ciągniki/naczepy) jest zbyt mała '.
            'względem liczby generowanych dostaw. Zwiększ flotę albo zmniejsz DELIVERIES_PER_MONTH/'.
            'TRANSPORT_SETS_PER_DELIVERY.'
        );
    }

    /**
     * Same overlap rule as WithDeliveryResourceAvailability::validateResourceAvailability() -
     * a resource already booked on a non-draft, non-cancelled transport set overlapping this
     * window is unavailable, regardless of which delivery it belongs to.
     *
     * @template TModel of Model
     *
     * @param  Collection<int, TModel>  $pool
     * @return TModel|null
     */
    private function availableResource(Collection $pool, string $column, Carbon $loadingAt, Carbon $unloadingAt): ?Model
    {
        $busyIds = DeliveryTransportSet::query()
            ->whereIn($column, $pool->pluck('id'))
            ->whereNotIn('status', [
                DeliveryTransportSetStatusEnum::DRAFT->value,
                DeliveryTransportSetStatusEnum::CANCELLED->value,
            ])
            ->where('loading_at', '<', $unloadingAt)
            ->where('unloading_at', '>', $loadingAt)
            ->pluck($column);

        $available = $pool->whereNotIn('id', $busyIds);

        return $available->isNotEmpty() ? $available->random() : null;
    }

    /**
     * Keeps a random day but snaps the time to a full or half hour (9:00, 10:30, 14:30, 17:00,
     * ...) within a plausible loading window, instead of an arbitrary minute/second - real
     * logistics slots are booked on the hour or half hour.
     */
    private function roundToHalfHour(\DateTimeInterface $day): Carbon
    {
        return Carbon::instance($day)->setTime(fake()->numberBetween(6, 18), fake()->randomElement([0, 30]));
    }

    /**
     * Derives a status from the transport set's own schedule, so a set that already
     * happened reads as completed (or occasionally cancelled), one straddling "now" reads
     * as in progress, and a future one reads as merely assigned.
     */
    private function transportSetStatusFor(\DateTimeInterface $loadingAt, \DateTimeInterface $unloadingAt): DeliveryTransportSetStatusEnum
    {
        $now = now();

        return match (true) {
            $unloadingAt < $now => fake()->boolean(90)
                ? DeliveryTransportSetStatusEnum::COMPLETED
                : DeliveryTransportSetStatusEnum::CANCELLED,
            $loadingAt <= $now => fake()->randomElement([
                DeliveryTransportSetStatusEnum::LOADING,
                DeliveryTransportSetStatusEnum::UNLOADING,
                DeliveryTransportSetStatusEnum::IN_TRANSIT,
            ]),
            default => DeliveryTransportSetStatusEnum::ASSIGNED,
        };
    }

    /**
     * @param  Collection<int, Good>  $goods
     */
    private function attachGoods(DeliveryTransportSet $transportSet, Collection $goods): void
    {
        $count = min(fake()->numberBetween(...self::GOODS_PER_TRANSPORT_SET), $goods->count());

        $goods->random($count)->each(function (Good $good) use ($transportSet) {
            $unit = $good->units->isNotEmpty() ? $good->units->random() : $good->defaultUnit;

            $transportSet->goods()->create([
                'good_id' => $good->id,
                'unit_id' => $unit->id,
                'quantity' => $this->randomQuantity($unit),
            ]);
        });
    }

    /**
     * Bulk units (weight/volume) get quantities in the hundreds/thousands, piece-like
     * units (szt/pal/opak) stay in a realistic truckload-sized range.
     */
    private function randomQuantity(Unit $unit): float
    {
        return match ($unit->name) {
            'kg', 'g', 'l', 'ml' => fake()->randomFloat(2, 50, 24000),
            default => fake()->randomFloat(2, 1, 60),
        };
    }

    /**
     * A random mix of costs per delivery, split between "whole delivery" overheads and
     * costs tied to one specific transport set (fuel/tolls/driver pay for that trip).
     *
     * @param  Collection<int, DeliveryTransportSet>  $transportSets
     */
    private function createCosts(Delivery $delivery, Collection $transportSets): void
    {
        $costsCount = fake()->numberBetween(...self::COSTS_PER_DELIVERY);

        for ($i = 0; $i < $costsCount; $i++) {
            $attachToTransportSet = $transportSets->isNotEmpty() && fake()->boolean(60);

            $delivery->costs()->create([
                'delivery_transport_set_id' => $attachToTransportSet ? $transportSets->random()->id : null,
                'type' => fake()->randomElement(DeliveryCostTypeEnum::cases())->value,
                'amount' => fake()->numberBetween(1000, 500000), // 10-5000 PLN, in grosze
                'currency' => CurrencyEnum::PLN->value,
                'description' => fake()->optional()->sentence(),
            ]);
        }
    }
}
