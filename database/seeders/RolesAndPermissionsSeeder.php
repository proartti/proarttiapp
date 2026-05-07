<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed the application's roles and permissions.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'USERS.CREATE',
            'USERS.READ',
            'USERS.UPDATE',
            'USERS.DELETE',
            'ROLES.CREATE',
            'ROLES.READ',
            'ROLES.UPDATE',
            'ROLES.DELETE',
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        $permissionModels = Permission::query()
            ->whereIn('name', $permissions)
            ->get();

        $administratorRole = Role::findOrCreate('administrator', 'web');
        $clientRole = Role::findOrCreate('client', 'web');

        $administratorRole->syncPermissions($permissionModels);
        $clientRole->syncPermissions([]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}