<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;

class LoyaltyService
{
    public function __construct(protected SettingsService $settings) {}

    public function pointsPerEuro(): float
    {
        return (float) $this->settings->get('loyalty.points_per_euro');
    }

    public function redeemValue(): float
    {
        return (float) $this->settings->get('loyalty.redeem_value');
    }

    public function minRedeem(): int
    {
        return (int) $this->settings->get('loyalty.min_redeem');
    }

    public function awardForAppointment(Appointment $appointment): void
    {
        $points = (int) floor((float) $appointment->total * $this->pointsPerEuro());

        if ($points <= 0) {
            return;
        }

        // Idempotent: completing the same appointment twice must not double-award.
        $alreadyAwarded = $appointment->user->loyaltyPoints()
            ->where('appointment_id', $appointment->id)
            ->where('points', '>', 0)
            ->exists();

        if ($alreadyAwarded) {
            return;
        }

        $appointment->user->loyaltyPoints()->create([
            'appointment_id' => $appointment->id,
            'points' => $points,
            'description' => __('Points earned for appointment #:id', ['id' => $appointment->id]),
        ]);
    }

    public function redeem(User $user, Appointment $appointment, int $points): void
    {
        if ($points <= 0) {
            return;
        }

        $user->loyaltyPoints()->create([
            'appointment_id' => $appointment->id,
            'points' => -$points,
            'description' => __('Points redeemed on appointment #:id', ['id' => $appointment->id]),
        ]);
    }

    public function refund(Appointment $appointment): void
    {
        if ($appointment->points_redeemed <= 0) {
            return;
        }

        $alreadyRefunded = $appointment->user->loyaltyPoints()
            ->where('appointment_id', $appointment->id)
            ->where('description', 'like', 'Refund%')
            ->exists();

        if ($alreadyRefunded) {
            return;
        }

        $appointment->user->loyaltyPoints()->create([
            'appointment_id' => $appointment->id,
            'points' => $appointment->points_redeemed,
            'description' => 'Refund of redeemed points for appointment #'.$appointment->id,
        ]);
    }
}
