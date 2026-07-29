<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Resources managed through the standard CRUD pattern (view/create/edit/delete).
     */
    public const RESOURCES = [
        'drivers',
        'vehicles',
        'contractors',
        'contractor-addresses',
        'goods',
        'units',
        'deliveries',
        'users',
        'roles',
    ];

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('role_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        Schema::enableForeignKeyConstraints();

        foreach (self::RESOURCES as $resource) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                Permission::create(['name' => "{$resource}.{$action}"]);
            }
        }

        // Permissions themselves are code-defined (via this seeder), not user-created,
        // so there is no "permissions.create".
        foreach (['view', 'edit', 'delete'] as $action) {
            Permission::create(['name' => "permissions.{$action}"]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
