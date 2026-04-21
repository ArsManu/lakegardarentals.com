<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\ApartmentImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApartmentImage>
 */
class ApartmentImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'apartment_id' => Apartment::factory(),
            'path' => 'apartments/placeholder.jpg',
            'alt_text' => fake()->sentence(4),
            'sort_order' => 0,
            'is_cover' => false,
        ];
    }
}
