<?php

namespace Modules\APPOINTMENT\Database\Factories;

use Modules\APPOINTMENT\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            //
        ];
    }
}