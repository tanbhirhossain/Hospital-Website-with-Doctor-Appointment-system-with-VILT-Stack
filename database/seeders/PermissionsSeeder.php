<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */
            'dashboard.view',

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            /*
            |--------------------------------------------------------------------------
            | Appointments
            |--------------------------------------------------------------------------
            */
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'appointments.delete',
            'appointments.confirm',
            'appointments.cancel',
            'appointments.complete',
            'appointments.no-show',

            /*
            |--------------------------------------------------------------------------
            | Doctors
            |--------------------------------------------------------------------------
            */
            'doctors.view',
            'doctors.create',
            'doctors.edit',
            'doctors.delete',

            /*
            |--------------------------------------------------------------------------
            | Departments
            |--------------------------------------------------------------------------
            */
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',

            /*
            |--------------------------------------------------------------------------
            | Center of Excellence
            |--------------------------------------------------------------------------
            */
            'coe.view',
            'coe.create',
            'coe.edit',
            'coe.delete',

            /*
            |--------------------------------------------------------------------------
            | Blogs
            |--------------------------------------------------------------------------
            */
            'blogs.view',
            'blogs.create',
            'blogs.edit',
            'blogs.delete',
            'blogs.restore',
            'blogs.force-delete',

            /*
            |--------------------------------------------------------------------------
            | Services
            |--------------------------------------------------------------------------
            */
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',

            /*
            |--------------------------------------------------------------------------
            | Health Packages
            |--------------------------------------------------------------------------
            */
            'health-packages.view',
            'health-packages.create',
            'health-packages.edit',
            'health-packages.delete',

            /*
            |--------------------------------------------------------------------------
            | Hero Sliders
            |--------------------------------------------------------------------------
            */
            'hero-sliders.view',
            'hero-sliders.create',
            'hero-sliders.edit',
            'hero-sliders.delete',

            /*
            |--------------------------------------------------------------------------
            | Patient Reviews
            |--------------------------------------------------------------------------
            */
            'patient-reviews.view',
            'patient-reviews.create',
            'patient-reviews.edit',
            'patient-reviews.delete',
            'patient-reviews.approve',
            'patient-reviews.reject',

            /*
            |--------------------------------------------------------------------------
            | Client Reviews
            |--------------------------------------------------------------------------
            */
            'client-reviews.view',
            'client-reviews.create',
            'client-reviews.edit',
            'client-reviews.delete',

            /*
            |--------------------------------------------------------------------------
            | Contact Messages
            |--------------------------------------------------------------------------
            */
            'contact-messages.view',
            'contact-messages.delete',
            'contact-messages.read',
            'contact-messages.archive',
            'contact-messages.resolve',

            /*
            |--------------------------------------------------------------------------
            | Gallery
            |--------------------------------------------------------------------------
            */
            'gallery.view',

            'gallery-categories.view',
            'gallery-categories.create',
            'gallery-categories.edit',
            'gallery-categories.delete',

            'gallery-items.view',
            'gallery-items.create',
            'gallery-items.edit',
            'gallery-items.delete',

            /*
            |--------------------------------------------------------------------------
            | Email Marketing
            |--------------------------------------------------------------------------
            */
            'email-marketing.view',

            'campaigns.view',
            'campaigns.create',
            'campaigns.edit',
            'campaigns.delete',
            'campaigns.send',
            'campaigns.schedule',
            'campaigns.cancel',
            'campaigns.duplicate',
            'campaigns.test',

            'templates.view',
            'templates.create',
            'templates.edit',
            'templates.delete',
            'templates.duplicate',
            'templates.test',

            'subscribers.view',
            'subscribers.create',
            'subscribers.edit',
            'subscribers.delete',
            'subscribers.import',

            /*
            |--------------------------------------------------------------------------
            | Settings
            |--------------------------------------------------------------------------
            */
            'settings.view',
            'settings.profile',
            'settings.password',
            'settings.appearance',
        ];

        foreach (array_unique($permissions) as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

         /*
    |--------------------------------------------------------------------------
    | Super Admin Role
    |--------------------------------------------------------------------------
    */
    $superAdmin = Role::firstOrCreate([
        'name' => 'Super Admin',
        'guard_name' => 'web',
    ]);

    // Assign all permissions to Super Admin
    $superAdmin->syncPermissions(Permission::all());


        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}