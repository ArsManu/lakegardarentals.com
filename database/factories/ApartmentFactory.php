<?php

namespace Database\Factories;

use App\Models\Apartment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Apartment>
 */
class ApartmentFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true).' Garda';

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 9999),
            'short_description' => fake()->paragraph(),
            'full_description' => fake()->paragraphs(4, true),
            'ideal_for' => fake()->randomElement(['Couples', 'Families', 'Couples and small families']),
            'max_guests' => fake()->numberBetween(2, 6),
            'bedrooms' => fake()->numberBetween(1, 3),
            'bathrooms' => fake()->numberBetween(1, 2),
            'size_m2' => fake()->numberBetween(45, 120),
            'price_from' => fake()->randomFloat(2, 80, 250),
            'location_text' => 'Garda (VR), Lake Garda, Italy',
            'address' => 'Via Roma 1, 37016 Garda VR, Italy',
            'check_in_out_note' => 'Check-in from 15:00. Check-out by 10:00.',
            'featured_image' => null,
            'availability_note' => fake()->sentence(),
            'is_active' => true,
            'sort_order' => 0,
            'external_listing_url' => null,
            'license_cir' => null,
            'license_cin' => null,
            'meta_title' => null,
            'meta_description' => null,
            'canonical_url' => null,
            'og_title' => null,
            'og_description' => null,
        ];
    }
}
