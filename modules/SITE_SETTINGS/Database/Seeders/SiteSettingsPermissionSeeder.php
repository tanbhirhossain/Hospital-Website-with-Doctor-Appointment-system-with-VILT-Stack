<?php

namespace Modules\SITE_SETTINGS\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SiteSettingsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Site Settings
            'site-settings.view',
            'site-settings.edit',

            // Navigation Menus
            'navigation.manage',

            // Leadership Messages
            'leadership.view',
            'leadership.create',
            'leadership.edit',
            'leadership.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign all site-settings permissions to super admin role
        $superAdmin = Role::where('name', 'super admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }
    }
}
