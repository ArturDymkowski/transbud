<?php

namespace Database\Seeders;

use App\Models\Contractor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ContractorSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('contractors')->truncate();
        Schema::enableForeignKeyConstraints();

        Contractor::factory()->count(5)->create();
    }
}
