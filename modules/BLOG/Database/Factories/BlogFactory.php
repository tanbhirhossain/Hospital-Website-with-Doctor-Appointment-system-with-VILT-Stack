<?php

namespace Modules\BLOG\Database\Factories;

use Modules\BLOG\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Blog>
 */
class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        return [
            //
        ];
    }
}