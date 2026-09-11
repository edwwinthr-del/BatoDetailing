<?php

namespace App\Listeners;

use App\Events\AppointmentStatusChanged;
use App\Models\Appointment;
use App\Notifications\AppointmentCompleted;
use App\Notifications\BookingApproved;
use App\Notifications\BookingRejected;
use App\Services\InvoiceService;
use App\Services\LoyaltyService;

class HandleAppointmentStatusChange
{
    public function __construct(
        protected InvoiceService $invoices,
        protected LoyaltyService $loyalty,
    ) {}

    public function handle(AppointmentStatusChanged $event): void
    {
        $appointment = $event->appointment;

        match ($event->newStatus) {
            Appointment::STATUS_APPROVED => $appointment->user->notify(new BookingApproved($appointment)),
            Appointment::STATUS_REJECTED => $this->reject($appointment),
            Appointment::STATUS_CANCELLED => $this->loyalty->refund($appointment),
            Appointment::STATUS_COMPLETED => $this->complete($appointment),
            default => null,
        };
    }

    protected function reject(Appointment $appointment): void
    {
        $this->loyalty->refund($appointment);

        $appointment->user->notify(new BookingRejected($appointment));
    }

    protected function complete(Appointment $appointment): void
    {
        $this->loyalty->awardForAppointment($appointment);

        $appointment->user->notify(new AppointmentCompleted($appointment));

        // Generates the invoice + PDF and queues the invoice email.
        $this->invoices->generateFor($appointment);
    }
}
