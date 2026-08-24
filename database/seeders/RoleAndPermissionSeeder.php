<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // findOrCreate()/syncPermissions() read through this cache; a leftover
        // entry from a previous run (e.g. a truncated table) would otherwise
        // make freshly-created permissions invisible within this same run.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'films.manage',
            'sessions.manage',
            'halls.manage',
            'bookings.manage-any',
            'users.manage-roles',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $admin = Role::findOrCreate('admin');
        $admin->syncPermissions($permissions);

        $manager = Role::findOrCreate('manager');
        $manager->syncPermissions([
            'sessions.manage',
            'bookings.manage-any',
        ]);

        // No special permissions: ownership-based access only (see Policies).
        Role::findOrCreate('customer');
    }
}
