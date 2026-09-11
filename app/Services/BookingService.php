<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\BookingCreated;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        protected SettingsService $settings,
        protected PriceCalculator $calculator,
        protected LoyaltyService $loyalty,
    ) {}

    /**
     * @param  array{vehicle_id: int, service_ids: array<int>, scheduled_at: string, notes?: string|null, redeem_points?: int|null}  $data
     */
    public function book(User $user, array $data): Appointment
    {
        $scheduledAt = Carbon::parse($data['scheduled_at'])->startOfHour();

        $this->assertWithinBusinessHours($scheduledAt);
        $this->assertSlotAvailable($scheduledAt);

        $vehicle = Vehicle::findOrFail($data['vehicle_id']);
        $services = Service::whereIn('id', $data['service_ids'])->where('is_active', true)->get();

        $pointsRequested = (int) ($data['redeem_points'] ?? 0);

        if ($pointsRequested > 0) {
            $this->assertRedeemable($user, $pointsRequested);
        }

        $price = $this->calculator->calculate($vehicle, $services, $pointsRequested);

        $appointment = DB::transaction(function () use ($user, $vehicle, $services, $scheduledAt, $data, $price): Appointment {
            $appointment = $user->appointments()->create([
                'vehicle_id' => $vehicle->id,
                'scheduled_at' => $scheduledAt,
                'status' => Appointment::STATUS_PENDING,
                'subtotal' => $price['subtotal'],
                'type_modifier' => $price['type_modifier'],
                'discount' => $price['discount'],
                'total' => $price['total'],
                'points_redeemed' => $price['points_redeemed'],
                'notes' => $data['notes'] ?? null,
            ]);

            $appointment->services()->attach(
                $services->mapWithKeys(fn (Service $service) => [$service->id => ['price' => $service->base_price]])->all()
            );

            if ($price['points_redeemed'] > 0) {
                $this->loyalty->redeem($user, $appointment, $price['points_redeemed']);
            }

            return $appointment;
        });

        $user->notify(new BookingCreated($appointment));

        return $appointment;
    }

    public function assertWithinBusinessHours(CarbonInterface $at): void
    {
        $hours = $this->settings->businessHours();

        $withinDays = in_array($at->isoWeekday(), $hours['days']);
        $withinHours = $at->hour >= $hours['open'] && $at->hour < $hours['close'];

        if (! $withinDays || ! $withinHours) {
            throw ValidationException::withMessages([
                'scheduled_at' => __('The selected time is outside our working hours.'),
            ]);
        }
    }

    public function assertSlotAvailable(CarbonInterface $at, ?int $ignoreAppointmentId = null): void
    {
        $taken = Appointment::where('scheduled_at', $at)
            ->whereNotIn('status', [Appointment::STATUS_CANCELLED, Appointment::STATUS_REJECTED])
            ->when($ignoreAppointmentId, fn ($query) => $query->where('id', '!=', $ignoreAppointmentId))
            ->exists();

        if ($taken) {
            throw ValidationException::withMessages([
                'scheduled_at' => __('That time slot is already booked. Please pick another one.'),
            ]);
        }
    }

    protected function assertRedeemable(User $user, int $points): void
    {
        if ($points > $user->loyaltyBalance()) {
            throw ValidationException::withMessages([
                'redeem_points' => __('You do not have enough loyalty points.'),
            ]);
        }

        if ($points < $this->loyalty->minRedeem()) {
            throw ValidationException::withMessages([
                'redeem_points' => __('A minimum of :min points is required to redeem.', ['min' => $this->loyalty->minRedeem()]),
            ]);
        }
    }
}
