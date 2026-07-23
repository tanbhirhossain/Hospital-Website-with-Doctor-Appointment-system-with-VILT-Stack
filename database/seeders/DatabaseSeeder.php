<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\WEBSITE\Database\Seeders\DepartmentSeeder;
use Modules\WEBSITE\Database\Seeders\DoctorSeeder;
use Modules\WEBSITE\Database\Seeders\DoctorTimetableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::query()->firstOrCreate([
            'email' => 'amz.tanbhir@gmail.com',
        ], [
            'name' => 'TANBHIR HOSSAIN',
            'password' => Hash::make('password'),
        ]);

        $this->call(AclPermissionSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(DoctorSeeder::class);
        $this->call(DoctorTimetableSeeder::class);

    }
}
