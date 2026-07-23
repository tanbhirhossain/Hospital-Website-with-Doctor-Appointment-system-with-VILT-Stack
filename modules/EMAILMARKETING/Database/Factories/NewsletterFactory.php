<?php

namespace Modules\EMAILMARKETING\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\EMAILMARKETING\Models\Newsletter;

/**
 * @extends Factory<Newsletter>
 */
class NewsletterFactory extends Factory
{
    protected $model = Newsletter::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'source' => fake()->randomElement(['website', 'blog', 'appointment', 'health-camp']),
            'isActive' => true,
            'status' => 'subscribed',
            'audience_type' => fake()->randomElement(['patients', 'doctors', 'community', 'corporate']),
            'tags' => fake()->randomElements(['diabetes', 'cardiology', 'women-health', 'child-care', 'general'], fake()->numberBetween(1, 3)),
            'country' => 'Bangladesh',
            'subscribed_at' => now()->subDays(fake()->numberBetween(0, 90)),
        ];
    }
}
