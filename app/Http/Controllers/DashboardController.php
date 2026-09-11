<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'vehicleCount' => $user->vehicles()->count(),
            'appointmentCount' => $user->appointments()->count(),
            'loyaltyBalance' => $user->loyaltyBalance(),
            'upcomingAppointment' => $user->appointments()
                ->with(['vehicle', 'services'])
                ->where('scheduled_at', '>=', now())
                ->whereNotIn('status', [Appointment::STATUS_CANCELLED, Appointment::STATUS_REJECTED])
                ->orderBy('scheduled_at')
                ->first(),
        ]);
    }
}
