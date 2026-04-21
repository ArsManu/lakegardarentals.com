<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\ApartmentSeason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApartmentSeason>
 */
class ApartmentSeasonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'apartment_id' => Apartment::factory(),
            'label' => fake()->randomElement(['High season', 'Mid season', 'Low season']),
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'price_per_night' => fake()->randomFloat(2, 90, 220),
            'sort_order' => 0,
        ];
    }
}
