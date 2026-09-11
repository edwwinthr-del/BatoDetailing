<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'brand' => fake()->randomElement(['BMW', 'Audi', 'Mercedes-Benz', 'Volkswagen', 'Toyota', 'Škoda']),
            'model' => fake()->randomElement(['320d', 'A4', 'C200', 'Golf', 'Corolla', 'Octavia']),
            'year' => fake()->numberBetween(2005, 2026),
            'type' => fake()->randomElement(Vehicle::TYPES),
            'license_plate' => strtoupper(fake()->bothify('NS-###-??')),
            'color' => fake()->safeColorName(),
            'image_path' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
