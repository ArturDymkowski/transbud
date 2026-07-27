<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('roles')->truncate();
        Schema::enableForeignKeyConstraints();

        $admin = Role::create(['name' => 'Admin']);
        $admin->syncPermissions(Permission::all());

        $user = Role::create(['name' => 'User']);
        $user->syncPermissions(
            Permission::whereIn('name', [
                'drivers.view',
                'vehicles.view',
                'contractors.view',
                'contractor-addresses.view',
                'goods.view',
                'units.view',
            ])->get()
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
