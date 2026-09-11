<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 20, 300);
        $modifier = fake()->randomElement([0, 5, 15, 20]);

        return [
            'user_id' => User::factory(),
            'vehicle_id' => fn (array $attributes) => Vehicle::factory()->create(['user_id' => $attributes['user_id']])->id,
            'scheduled_at' => Carbon::parse(fake()->dateTimeBetween('+1 day', '+1 month'))->setHour(fake()->numberBetween(8, 17))->startOfHour(),
            'status' => Appointment::STATUS_PENDING,
            'subtotal' => $subtotal,
            'type_modifier' => $modifier,
            'discount' => 0,
            'total' => $subtotal + $modifier,
            'points_redeemed' => 0,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Appointment::STATUS_COMPLETED,
            'scheduled_at' => Carbon::parse(fake()->dateTimeBetween('-1 month', '-1 day'))->setHour(fake()->numberBetween(8, 17))->startOfHour(),
        ]);
    }
}
