<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Foundation\Events\Dispatchable;

class AppointmentStatusChanged
{
    use Dispatchable;

    public function __construct(
        public Appointment $appointment,
        public string $oldStatus,
        public string $newStatus,
    ) {}
}
