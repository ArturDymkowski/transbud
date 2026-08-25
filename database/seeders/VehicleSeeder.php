<?php

namespace Database\Seeders;

use App\Enums\VehicleTypeEnum;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class UserTableSeeder.
 */
class VehicleSeeder extends Seeder
{
    /**
     * Day offsets (from "today", whenever the seeder happens to run) guaranteed to land in the
     * red (<=7 days), yellow (<=14 days) and green (<=30 days) buckets used by the dashboard
     * and table badges. See App\Helpers\ExpiryHelper.
     */
    private const EXPIRY_OFFSETS = [3, 10, 20];

    private const EXPIRY_FIELDS = [
        'technical_inspection_expiry_date',
        'insurance_expiry_date',
        'tachograph_inspection_expiry_date',
    ];

    private const TRACTOR_COUNT = 4;

    private const TRAILER_COUNT = 3;

    /**
     * Run the database seed.
     *
     * 7 vehicles total (4 tractors + 3 trailers) - enough for DeliverySeeder to build
     * varied transport sets from, without an unrealistically large fleet.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('vehicles')->truncate();
        Schema::enableForeignKeyConstraints();

        $tractors = Vehicle::factory()->count(self::TRACTOR_COUNT)->create(['type' => VehicleTypeEnum::TRACTOR->value]);
        $trailers = Vehicle::factory()->count(self::TRAILER_COUNT)->create(['type' => VehicleTypeEnum::TRAILER->value]);

        $this->pinExpiryDates($tractors->concat($trailers));
    }

    /**
     * Pin all three expiry dates on the first 3 vehicles (one per red/yellow/green offset),
     * so the dashboard's 30/14/7-day boxes always have something to show, regardless of when
     * this seeder runs. Sharing one offset across all three fields per vehicle covers every
     * bucket without needing a dedicated vehicle per field, which the 7-vehicle fleet has no
     * room for.
     */
    private function pinExpiryDates(Collection $vehicles): void
    {
        foreach (self::EXPIRY_OFFSETS as $index => $days) {
            $vehicles[$index]->update(array_fill_keys(self::EXPIRY_FIELDS, now()->addDays($days)));
        }
    }
}
