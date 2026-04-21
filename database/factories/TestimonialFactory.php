<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'author_name' => fake()->name(),
            'author_location' => fake()->randomElement(['United Kingdom', 'Germany', 'Netherlands', 'Italy']),
            'quote' => fake()->paragraph(),
            'rating' => fake()->numberBetween(4, 5),
            'sort_order' => 0,
            'is_published' => true,
        ];
    }
}
