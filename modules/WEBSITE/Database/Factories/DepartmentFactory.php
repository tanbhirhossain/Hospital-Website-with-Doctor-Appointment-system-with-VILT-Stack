<?php

namespace Modules\WEBSITE\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\WEBSITE\Models\Department;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'shortDesc' => fake()->sentence(),
            'descriptions' => fake()->paragraph(),
            'text_icon' => fake()->randomElement(['heart-pulse', 'stethoscope', 'activity']),
        ];
    }
}
