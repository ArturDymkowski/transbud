<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        activity()->withoutLogging(function () {
            $this->call([
                PermissionSeeder::class,
                RoleSeeder::class,
                UserSeeder::class,
                DriverSeeder::class,
                VehicleSeeder::class,
                ContractorSeeder::class,
                ContractorAddressSeeder::class,
                UnitSeeder::class,
                GoodSeeder::class,
                DeliverySeeder::class,
            ]);
        });
    }
}
