<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory()->completed(),
            'user_id' => fn (array $attributes) => Appointment::find($attributes['appointment_id'])?->user_id,
            'rating' => fake()->numberBetween(3, 5),
            'comment' => fake()->sentence(10),
        ];
    }
}
