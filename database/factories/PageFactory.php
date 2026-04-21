<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'title' => fake()->sentence(3),
            'blocks' => [],
            'meta_title' => null,
            'meta_description' => null,
            'canonical_url' => null,
            'og_title' => null,
            'og_description' => null,
        ];
    }
}
