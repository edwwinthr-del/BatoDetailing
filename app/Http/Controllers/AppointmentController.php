<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Vehicle;
use App\Services\BookingService;
use App\Services\LoyaltyService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        return view('appointments.index', [
            'appointments' => $request->user()->appointments()
                ->with(['vehicle', 'services', 'invoice', 'review'])
                ->orderByDesc('scheduled_at')
                ->paginate(10),
        ]);
    }

    public function create(Request $request, SettingsService $settings, LoyaltyService $loyalty): View
    {
        return view('appointments.create', [
            'vehicles' => $request->user()->vehicles()->orderBy('brand')->get(),
            'services' => Service::where('is_active', true)->orderBy('base_price')->get(),
            'modifiers' => collect(Vehicle::TYPES)
                ->mapWithKeys(fn (string $type) => [$type => $settings->modifierFor($type)]),
            'businessHours' => $settings->businessHours(),
            'loyaltyBalance' => $request->user()->loyaltyBalance(),
            'redeemValue' => $loyalty->redeemValue(),
            'minRedeem' => $loyalty->minRedeem(),
        ]);
    }

    public function store(StoreAppointmentRequest $request, BookingService $booking): RedirectResponse
    {
        $appointment = $booking->book($request->user(), $request->validated());

        return redirect()->route('appointments.show', $appointment)
            ->with('status', __('Appointment booked! We will confirm it shortly.'));
    }

    public function show(Appointment $appointment): View
    {
        Gate::authorize('view', $appointment);

        return view('appointments.show', [
            'appointment' => $appointment->load(['vehicle', 'services', 'invoice', 'review']),
        ]);
    }

    public function cancel(Appointment $appointment, LoyaltyService $loyalty): RedirectResponse
    {
        Gate::authorize('cancel', $appointment);

        $appointment->update(['status' => Appointment::STATUS_CANCELLED]);

        $loyalty->refund($appointment);

        return redirect()->route('appointments.show', $appointment)->with('status', __('Appointment cancelled.'));
    }
}
