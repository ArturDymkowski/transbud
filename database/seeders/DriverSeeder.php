<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class UserTableSeeder.
 */
class DriverSeeder extends Seeder
{
    /**
     * Day offsets (from "today", whenever the seeder happens to run) guaranteed to land in the
     * red (<=7 days), yellow (<=14 days) and green (<=30 days) buckets used by the dashboard
     * and table badges. See App\Helpers\ExpiryHelper.
     */
    private const EXPIRY_OFFSETS = [3, 10, 20];

    private const EXPIRY_FIELDS = [
        'driving_license_expiry_date',
        'identity_card_expiry_date',
    ];

    /**
     * Run the database seed.
     *
     * 12 drivers total, all produced by seedExpiringDrivers(): 2 fields x 3 offsets x
     * 2 drivers each. No separate batch of "plain" random drivers is needed on top -
     * pinning one expiry field still leaves the other one random, so the fleet stays varied.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('drivers')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->seedExpiringDrivers();
    }

    /**
     * A handful of drivers per date field, with expiry dates pinned so the dashboard's
     * 30/14/7-day boxes always have something to show, regardless of when this seeder runs.
     */
    private function seedExpiringDrivers(): void
    {
        foreach (self::EXPIRY_FIELDS as $field) {
            foreach (self::EXPIRY_OFFSETS as $days) {
                Driver::factory()->count(2)->create([
                    $field => now()->addDays($days),
                ]);
            }
        }
    }
}
