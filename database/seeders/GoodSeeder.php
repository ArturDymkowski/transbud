<?php

namespace Database\Seeders;

use App\Models\Good;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GoodSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('goods')->truncate();
        Schema::enableForeignKeyConstraints();

        $unitIds = Unit::pluck('id');

        collect(range(1, 20))->each(function () use ($unitIds) {
            $good = Good::factory()->create([
                'default_unit_id' => $unitIds->random(),
            ]);

            $good->units()->sync(
                $unitIds->random(min(3, $unitIds->count()))
            );
        });
    }
}
