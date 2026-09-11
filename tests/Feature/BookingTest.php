<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\LoyaltyPoint;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\BookingCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected function validSlot(): Carbon
    {
        // Next Monday at 10:00 — inside default business hours (Mon–Sat, 8–18).
        return now()->next('monday')->setTime(10, 0);
    }

    public function test_user_can_book_with_correct_price_calculation(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user)->create(['type' => 'suv']);
        $service = Service::factory()->create(['base_price' => 50]);

        $response = $this->actingAs($user)->post('/appointments', [
            'vehicle_id' => $vehicle->id,
            'service_ids' => [$service->id],
            'scheduled_at' => $this->validSlot()->format('Y-m-d H:i'),
        ]);

        $appointment = Appointment::first();
        $this->assertNotNull($appointment);
        $response->assertRedirect(route('appointments.show', $appointment));

        // 50 (service) + 15 (default SUV modifier) = 65
        $this->assertEquals(50.00, (float) $appointment->subtotal);
        $this->assertEquals(15.00, (float) $appointment->type_modifier);
        $this->assertEquals(65.00, (float) $appointment->total);
        $this->assertSame(Appointment::STATUS_PENDING, $appointment->status);
        $this->assertEquals(50.00, (float) $appointment->services->first()->pivot->price);

        Notification::assertSentTo($user, BookingCreated::class);
    }

    public function test_double_booking_same_slot_is_prevented(): void
    {
        Notification::fake();

        $slot = $this->validSlot();

        $existing = Appointment::factory()->create(['scheduled_at' => $slot]);

        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user)->create();
        $service = Service::factory()->create();

        $response = $this->actingAs($user)->post('/appointments', [
            'vehicle_id' => $vehicle->id,
            'service_ids' => [$service->id],
            'scheduled_at' => $slot->format('Y-m-d H:i'),
        ]);

        $response->assertSessionHasErrors('scheduled_at');
        $this->assertSame(1, Appointment::count());
    }

    public function test_booking_outside_business_hours_is_rejected(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->for($user)->create();
        $service = Service::factory()->create();

        // 22:00 is after closing (18:00).
        $response = $this->actingAs($user)->post('/appointments', [
            'vehicle_id' => $vehicle->id,
            'service_ids' => [$service->id],
            'scheduled_at' => now()->next('monday')->setTime(22, 0)->format('Y-m-d H:i'),
        ]);

        $response->assertSessionHasErrors('scheduled_at');

        // Sunday is not a working day by default.
        $response = $this->actingAs($user)->post('/appointments', [
            'vehicle_id' => $vehicle->id,
            'service_ids' => [$service->id],
            'scheduled_at' => now()->next('sunday')->setTime(10, 0)->format('Y-m-d H:i'),
        ]);

        $response->assertSessionHasErrors('scheduled_at');
        $this->assertSame(0, Appointment::count());
    }

    public function test_user_cannot_book_with_someone_elses_vehicle(): void
    {
        $user = User::factory()->create();
        $otherVehicle = Vehicle::factory()->create();
        $service = Service::factory()->create();

        $response = $this->actingAs($user)->post('/appointments', [
            'vehicle_id' => $otherVehicle->id,
            'service_ids' => [$service->id],
            'scheduled_at' => $this->validSlot()->format('Y-m-d H:i'),
        ]);

        $response->assertSessionHasErrors('vehicle_id');
    }

    public function test_user_can_redeem_loyalty_points_for_discount(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        LoyaltyPoint::factory()->for($user)->create(['points' => 200]);

        $vehicle = Vehicle::factory()->for($user)->create(['type' => 'hatchback']);
        $service = Service::factory()->create(['base_price' => 100]);

        $this->actingAs($user)->post('/appointments', [
            'vehicle_id' => $vehicle->id,
            'service_ids' => [$service->id],
            'scheduled_at' => $this->validSlot()->format('Y-m-d H:i'),
            'redeem_points' => 100,
        ])->assertSessionHasNoErrors();

        $appointment = Appointment::first();

        // 100 points × 0.05 € = 5 € discount; hatchback modifier = 0.
        $this->assertEquals(5.00, (float) $appointment->discount);
        $this->assertEquals(95.00, (float) $appointment->total);
        $this->assertSame(100, $appointment->points_redeemed);
        $this->assertSame(100, $user->fresh()->loyaltyBalance());
    }

    public function test_redeeming_more_points_than_owned_fails(): void
    {
        $user = User::factory()->create();
        LoyaltyPoint::factory()->for($user)->create(['points' => 50]);

        $vehicle = Vehicle::factory()->for($user)->create();
        $service = Service::factory()->create();

        $response = $this->actingAs($user)->post('/appointments', [
            'vehicle_id' => $vehicle->id,
            'service_ids' => [$service->id],
            'scheduled_at' => $this->validSlot()->format('Y-m-d H:i'),
            'redeem_points' => 150,
        ]);

        $response->assertSessionHasErrors('redeem_points');
    }

    public function test_user_can_cancel_pending_appointment_and_points_are_refunded(): void
    {
        $appointment = Appointment::factory()->create(['points_redeemed' => 100, 'discount' => 5]);
        $appointment->user->loyaltyPoints()->create([
            'appointment_id' => $appointment->id,
            'points' => -100,
            'description' => 'Points redeemed on appointment #'.$appointment->id,
        ]);

        $this->actingAs($appointment->user)
            ->patch("/appointments/{$appointment->id}/cancel")
            ->assertRedirect(route('appointments.show', $appointment));

        $this->assertSame(Appointment::STATUS_CANCELLED, $appointment->fresh()->status);
        $this->assertSame(0, $appointment->user->fresh()->loyaltyBalance());
    }

    public function test_user_cannot_cancel_completed_appointment(): void
    {
        $appointment = Appointment::factory()->completed()->create();

        $this->actingAs($appointment->user)
            ->patch("/appointments/{$appointment->id}/cancel")
            ->assertForbidden();
    }

    public function test_user_cannot_view_someone_elses_appointment(): void
    {
        $appointment = Appointment::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($other)->get("/appointments/{$appointment->id}")->assertForbidden();
    }
}
