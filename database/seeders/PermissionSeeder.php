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

        // Spatie caches the permission list; without clearing it here, Permission::create()
        // below would still see the pre-truncate permissions and throw "already exists".
        app(PermissionRegistrar::class)->forgetCachedPermissions();

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
