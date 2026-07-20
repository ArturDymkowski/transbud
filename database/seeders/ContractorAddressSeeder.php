<?php

namespace Database\Seeders;

use App\Models\Contractor;
use App\Models\ContractorAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ContractorAddressSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('contractor_addresses')->truncate();
        Schema::enableForeignKeyConstraints();

        Contractor::all()->each(function (Contractor $contractor) {
            ContractorAddress::factory()
                ->count(fake()->numberBetween(1, 3))
                ->create(['contractor_id' => $contractor->id]);
        });
    }
}
