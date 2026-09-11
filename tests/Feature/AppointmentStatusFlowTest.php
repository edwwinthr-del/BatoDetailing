<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\User;
use App\Notifications\AppointmentCompleted;
use App\Notifications\BookingApproved;
use App\Notifications\BookingRejected;
use App\Notifications\InvoiceSent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AppointmentStatusFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_approving_sends_notification(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $appointment = Appointment::factory()->create();

        $this->actingAs($admin)->patch("/admin/appointments/{$appointment->id}/status", [
            'status' => Appointment::STATUS_APPROVED,
        ])->assertRedirect();

        $this->assertSame(Appointment::STATUS_APPROVED, $appointment->fresh()->status);
        Notification::assertSentTo($appointment->user, BookingApproved::class);
    }

    public function test_completing_generates_invoice_pdf_awards_points_and_notifies(): void
    {
        Notification::fake();
        Storage::fake();

        $admin = User::factory()->admin()->create();
        $appointment = Appointment::factory()->create([
            'subtotal' => 100,
            'type_modifier' => 15,
            'total' => 115,
        ]);
        $appointment->services()->attach(Service::factory()->create(['base_price' => 100]), ['price' => 100]);

        $this->actingAs($admin)->patch("/admin/appointments/{$appointment->id}/status", [
            'status' => Appointment::STATUS_COMPLETED,
        ])->assertRedirect();

        // Invoice created with the spec number format and stored PDF.
        $invoice = Invoice::first();
        $this->assertNotNull($invoice);
        $this->assertMatchesRegularExpression('/^BATO-\d{4}-\d{2}-\d{6}$/', $invoice->number);
        $this->assertSame($appointment->id, $invoice->appointment_id);
        $this->assertEquals(115.00, (float) $invoice->total);
        $this->assertSame(Invoice::STATUS_ISSUED, $invoice->status);
        $this->assertNotNull($invoice->pdf_path);
        Storage::assertExists($invoice->pdf_path);

        // Loyalty points awarded: floor(115 × 1 point/€) = 115.
        $this->assertSame(115, $appointment->user->fresh()->loyaltyBalance());

        Notification::assertSentTo($appointment->user, AppointmentCompleted::class);
        Notification::assertSentTo($appointment->user, InvoiceSent::class);
    }

    public function test_completing_twice_does_not_duplicate_invoice_or_points(): void
    {
        Notification::fake();
        Storage::fake();

        $admin = User::factory()->admin()->create();
        $appointment = Appointment::factory()->create(['total' => 100]);

        foreach ([Appointment::STATUS_COMPLETED, Appointment::STATUS_IN_PROGRESS, Appointment::STATUS_COMPLETED] as $status) {
            $this->actingAs($admin)->patch("/admin/appointments/{$appointment->id}/status", ['status' => $status]);
        }

        $this->assertSame(1, Invoice::count());
        $this->assertSame(100, $appointment->user->fresh()->loyaltyBalance());
    }

    public function test_rejecting_refunds_redeemed_points_and_notifies(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $appointment = Appointment::factory()->create(['points_redeemed' => 50]);
        $appointment->user->loyaltyPoints()->create([
            'appointment_id' => $appointment->id,
            'points' => -50,
            'description' => 'Points redeemed on appointment #'.$appointment->id,
        ]);

        $this->actingAs($admin)->patch("/admin/appointments/{$appointment->id}/status", [
            'status' => Appointment::STATUS_REJECTED,
        ]);

        $this->assertSame(Appointment::STATUS_REJECTED, $appointment->fresh()->status);
        $this->assertSame(0, $appointment->user->fresh()->loyaltyBalance());
        Notification::assertSentTo($appointment->user, BookingRejected::class);
    }

    public function test_non_admin_cannot_change_status(): void
    {
        $appointment = Appointment::factory()->create();

        $this->actingAs($appointment->user)->patch("/admin/appointments/{$appointment->id}/status", [
            'status' => Appointment::STATUS_APPROVED,
        ])->assertForbidden();
    }

    public function test_user_can_download_invoice_pdf(): void
    {
        Notification::fake();
        Storage::fake();

        $admin = User::factory()->admin()->create();
        $appointment = Appointment::factory()->create();

        $this->actingAs($admin)->patch("/admin/appointments/{$appointment->id}/status", [
            'status' => Appointment::STATUS_COMPLETED,
        ]);

        $invoice = Invoice::first();

        $this->actingAs($appointment->user)
            ->get("/invoices/{$invoice->id}/download")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        // Another user cannot download it.
        $this->actingAs(User::factory()->create())
            ->get("/invoices/{$invoice->id}/download")
            ->assertForbidden();
    }
}
