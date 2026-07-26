<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('units')->truncate();
        Schema::enableForeignKeyConstraints();

        collect(['kg', 'g', 'l', 'ml', 'm3', 'szt', 'pal', 'opak'])
            ->each(fn (string $name) => Unit::create(['name' => $name]));
    }
}
