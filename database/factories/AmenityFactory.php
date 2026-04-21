<?php

namespace Database\Factories;

use App\Models\Amenity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Amenity>
 */
class AmenityFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['Wi‑Fi', 'Air conditioning', 'Parking', 'Lake view', 'Kitchen', 'Washing machine']);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'icon_key' => null,
            'sort_order' => 0,
        ];
    }
}
