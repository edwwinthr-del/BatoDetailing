<?php

namespace App\Http\Controllers\Admin;

use App\Events\AppointmentStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.appointments.index', [
            'appointments' => Appointment::with(['user', 'vehicle', 'services'])
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->when($request->filled('search'), function ($query) use ($request): void {
                    $search = '%'.$request->string('search').'%';
                    $query->whereHas('user', fn ($q) => $q
                        ->where('first_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('email', 'like', $search));
                })
                ->orderByDesc('scheduled_at')
                ->paginate(20)
                ->withQueryString(),
            'statuses' => Appointment::STATUSES,
        ]);
    }

    public function calendar(Request $request): View
    {
        $month = Carbon::createFromFormat('Y-m', $request->query('month', now()->format('Y-m')))->startOfMonth();

        $appointments = Appointment::with(['user', 'vehicle'])
            ->whereBetween('scheduled_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->orderBy('scheduled_at')
            ->get()
            ->groupBy(fn (Appointment $appointment): string => $appointment->scheduled_at->format('Y-m-d'));

        return view('admin.appointments.calendar', [
            'month' => $month,
            'appointments' => $appointments,
        ]);
    }

    public function show(Appointment $appointment): View
    {
        return view('admin.appointments.show', [
            'appointment' => $appointment->load(['user', 'vehicle', 'services', 'invoice', 'review', 'comments.user']),
            'statuses' => Appointment::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Appointment::STATUSES)],
        ]);

        $oldStatus = $appointment->status;

        if ($oldStatus === $validated['status']) {
            return back();
        }

        $appointment->update(['status' => $validated['status']]);

        event(new AppointmentStatusChanged($appointment, $oldStatus, $validated['status']));

        return back()->with('status', __('Appointment status updated.'));
    }
}
