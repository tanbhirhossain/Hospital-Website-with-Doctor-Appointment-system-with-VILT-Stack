<?php

namespace Modules\WEBSITE\Database\Factories;

use Modules\WEBSITE\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            //
        ];
    }
}