<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\User;
use App\Notifications\AppointmentCompleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkerTest extends TestCase
{
    use RefreshDatabase;

    public function test_worker_can_view_work_queue(): void
    {
        $worker = User::factory()->worker()->create();
        Appointment::factory()->create(['status' => Appointment::STATUS_APPROVED]);

        $this->actingAs($worker)->get('/worker')->assertOk();
    }

    public function test_admin_can_also_view_work_queue(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/worker')->assertOk();
    }

    public function test_regular_user_cannot_access_worker_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/worker')->assertForbidden();
    }

    public function test_worker_cannot_access_admin_panel(): void
    {
        $worker = User::factory()->worker()->create();

        $this->actingAs($worker)->get('/admin')->assertForbidden();
    }

    public function test_worker_is_redirected_to_work_queue_after_login(): void
    {
        $worker = User::factory()->worker()->create();

        $this->post('/login', [
            'email' => $worker->email,
            'password' => 'password',
        ])->assertRedirect(route('worker.appointments.index'));
    }

    public function test_worker_can_start_and_finish_a_job_which_generates_invoice(): void
    {
        Notification::fake();
        Storage::fake();

        $worker = User::factory()->worker()->create();
        $appointment = Appointment::factory()->create(['status' => Appointment::STATUS_APPROVED, 'total' => 80]);

        $this->actingAs($worker)->patch("/worker/appointments/{$appointment->id}/status", [
            'status' => Appointment::STATUS_IN_PROGRESS,
        ])->assertRedirect();

        $this->assertSame(Appointment::STATUS_IN_PROGRESS, $appointment->fresh()->status);

        $this->actingAs($worker)->patch("/worker/appointments/{$appointment->id}/status", [
            'status' => Appointment::STATUS_COMPLETED,
        ])->assertRedirect();

        $this->assertSame(Appointment::STATUS_COMPLETED, $appointment->fresh()->status);
        $this->assertSame(1, Invoice::count());
        $this->assertSame(80, $appointment->user->fresh()->loyaltyBalance());
        Notification::assertSentTo($appointment->user, AppointmentCompleted::class);
    }

    public function test_worker_can_cancel_a_job(): void
    {
        $worker = User::factory()->worker()->create();
        $appointment = Appointment::factory()->create(['status' => Appointment::STATUS_APPROVED]);

        $this->actingAs($worker)->patch("/worker/appointments/{$appointment->id}/status", [
            'status' => Appointment::STATUS_CANCELLED,
        ])->assertRedirect();

        $this->assertSame(Appointment::STATUS_CANCELLED, $appointment->fresh()->status);
    }

    public function test_worker_cannot_approve_or_reject(): void
    {
        $worker = User::factory()->worker()->create();
        $appointment = Appointment::factory()->create();

        foreach ([Appointment::STATUS_APPROVED, Appointment::STATUS_REJECTED] as $status) {
            $this->actingAs($worker)->patch("/worker/appointments/{$appointment->id}/status", [
                'status' => $status,
            ])->assertSessionHasErrors('status');
        }

        $this->assertSame(Appointment::STATUS_PENDING, $appointment->fresh()->status);
    }

    public function test_worker_cannot_reopen_a_finished_job(): void
    {
        $worker = User::factory()->worker()->create();
        $appointment = Appointment::factory()->completed()->create();

        $this->actingAs($worker)->patch("/worker/appointments/{$appointment->id}/status", [
            'status' => Appointment::STATUS_IN_PROGRESS,
        ])->assertForbidden();
    }

    public function test_worker_can_leave_a_comment(): void
    {
        $worker = User::factory()->worker()->create();
        $appointment = Appointment::factory()->create();

        $this->actingAs($worker)->post("/worker/appointments/{$appointment->id}/comments", [
            'body' => 'Deep scratches on the hood, informed the customer.',
        ])->assertRedirect();

        $this->assertDatabaseHas('appointment_comments', [
            'appointment_id' => $appointment->id,
            'user_id' => $worker->id,
            'body' => 'Deep scratches on the hood, informed the customer.',
        ]);
    }

    public function test_customer_does_not_see_internal_comments(): void
    {
        $worker = User::factory()->worker()->create();
        $appointment = Appointment::factory()->create();
        $appointment->comments()->create([
            'user_id' => $worker->id,
            'body' => 'Secret internal note about this job.',
        ]);

        $this->actingAs($appointment->user)
            ->get("/appointments/{$appointment->id}")
            ->assertOk()
            ->assertDontSee('Secret internal note');
    }
}
