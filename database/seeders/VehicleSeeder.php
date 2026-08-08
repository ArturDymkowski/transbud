<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;
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

    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('vehicles')->truncate();
        Schema::enableForeignKeyConstraints();

        Vehicle::factory()->count(40)->create();

        $this->seedExpiringVehicles();
    }

    /**
     * A handful of vehicles per date field, with expiry dates pinned so the dashboard's
     * 30/14/7-day boxes always have something to show, regardless of when this seeder runs.
     */
    private function seedExpiringVehicles(): void
    {
        foreach (self::EXPIRY_FIELDS as $field) {
            foreach (self::EXPIRY_OFFSETS as $days) {
                Vehicle::factory()->count(2)->create([
                    $field => now()->addDays($days),
                ]);
            }
        }
    }
}
