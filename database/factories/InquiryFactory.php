<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inquiry>
 */
class InquiryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => Inquiry::TYPE_BOOKING,
            'apartment_id' => Apartment::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'check_in' => now()->addDays(7),
            'check_out' => now()->addDays(14),
            'guests' => 2,
            'message' => fake()->optional()->sentence(),
            'consent_at' => now(),
            'status' => Inquiry::STATUS_NEW,
            'source_page' => 'contact',
            'ip' => fake()->ipv4(),
            'read_at' => null,
        ];
    }
}
