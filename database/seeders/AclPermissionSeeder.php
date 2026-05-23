<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AclPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $role = Role::findOrCreate('Administrator', 'web');
        $role->syncPermissions($permissions);

        $superAdmin = Role::findOrCreate('Super Admin', 'web');
        $superAdmin->syncPermissions(Permission::query()->where('guard_name', 'web')->pluck('name'));

        User::query()
            ->where('email', 'test@example.com')
            ->first()
            ?->assignRole($role);

        User::query()
            ->where('email', 'amz.tanbhir@gmail.com')
            ->first()
            ?->assignRole($superAdmin);
    }
}
