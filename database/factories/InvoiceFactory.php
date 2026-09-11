<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total = fake()->randomFloat(2, 20, 300);

        return [
            'number' => 'BATO-'.now()->format('Y-m').'-'.str_pad((string) fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'user_id' => fn (array $attributes) => Appointment::find($attributes['appointment_id'])?->user_id,
            'appointment_id' => Appointment::factory()->completed(),
            'subtotal' => $total,
            'discount' => 0,
            'total' => $total,
            'status' => Invoice::STATUS_ISSUED,
            'pdf_path' => null,
            'issued_at' => now(),
        ];
    }
}
