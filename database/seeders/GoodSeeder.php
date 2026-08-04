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

        $unitIds = Unit::pluck('id', 'name');

        $goods = [
            ['name' => 'Pszenica', 'description' => 'Ziarno pszenicy konsumpcyjnej', 'unit' => 'kg'],
            ['name' => 'Żyto', 'description' => 'Ziarno żyta paszowego', 'unit' => 'kg'],
            ['name' => 'Kukurydza', 'description' => 'Ziarno kukurydzy suszonej', 'unit' => 'kg'],
            ['name' => 'Cement portlandzki', 'description' => 'Cement workowany na paletach', 'unit' => 'pal'],
            ['name' => 'Stal zbrojeniowa', 'description' => 'Pręty stalowe żebrowane', 'unit' => 'kg'],
            ['name' => 'Cegła klinkierowa', 'description' => 'Cegła elewacyjna klinkierowa', 'unit' => 'pal'],
            ['name' => 'Stoły drewniane', 'description' => 'Stoły biurowe z litego drewna', 'unit' => 'szt'],
            ['name' => 'Krzesła biurowe', 'description' => 'Krzesła tapicerowane', 'unit' => 'szt'],
            ['name' => 'Płyty OSB', 'description' => 'Płyty OSB budowlane', 'unit' => 'szt'],
            ['name' => 'Nawóz azotowy', 'description' => 'Nawóz mineralny azotowy', 'unit' => 'kg'],
        ];

        foreach ($goods as $data) {
            $good = Good::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'default_unit_id' => $unitIds[$data['unit']],
                'is_active' => true,
            ]);

            $good->units()->sync(array_unique([$unitIds[$data['unit']], $unitIds->random()]));
        }
    }
}
