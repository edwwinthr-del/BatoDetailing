<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_review_completed_appointment(): void
    {
        $appointment = Appointment::factory()->completed()->create();

        $response = $this->actingAs($appointment->user)->post("/appointments/{$appointment->id}/review", [
            'rating' => 5,
            'comment' => 'Fantastic job, the car looks brand new!',
        ]);

        $response->assertRedirect(route('appointments.show', $appointment));
        $this->assertDatabaseHas('reviews', [
            'appointment_id' => $appointment->id,
            'user_id' => $appointment->user_id,
            'rating' => 5,
        ]);
    }

    public function test_only_one_review_per_appointment(): void
    {
        $appointment = Appointment::factory()->completed()->create();

        $this->actingAs($appointment->user)->post("/appointments/{$appointment->id}/review", ['rating' => 5]);

        $this->actingAs($appointment->user)->post("/appointments/{$appointment->id}/review", ['rating' => 1])
            ->assertForbidden();

        $this->assertSame(1, $appointment->fresh()->review()->count());
    }

    public function test_cannot_review_pending_appointment(): void
    {
        $appointment = Appointment::factory()->create();

        $this->actingAs($appointment->user)->post("/appointments/{$appointment->id}/review", ['rating' => 4])
            ->assertForbidden();
    }

    public function test_cannot_review_someone_elses_appointment(): void
    {
        $appointment = Appointment::factory()->completed()->create();
        $other = User::factory()->create();

        $this->actingAs($other)->post("/appointments/{$appointment->id}/review", ['rating' => 4])
            ->assertForbidden();
    }

    public function test_rating_must_be_between_one_and_five(): void
    {
        $appointment = Appointment::factory()->completed()->create();

        $this->actingAs($appointment->user)->post("/appointments/{$appointment->id}/review", ['rating' => 9])
            ->assertSessionHasErrors('rating');
    }
}
