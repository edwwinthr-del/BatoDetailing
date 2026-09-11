<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'base_price' => fake()->randomFloat(2, 10, 200),
            'duration_minutes' => fake()->randomElement([30, 60, 90, 120]),
            'is_active' => true,
        ];
    }
}
