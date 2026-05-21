<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\User;
use Database\DisableForeignKeys;
use Database\TruncateTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserTableSeeder.
 */
class DriverSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();
        $this->truncate('drivers');

        Driver::factory()->count(100)->create();

        $this->enableForeignKeys();
    }
}
