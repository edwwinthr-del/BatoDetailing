<?php

namespace App\Http\Controllers\Worker;

use App\Events\AppointmentStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WorkerAppointmentController extends Controller
{
    /**
     * Statuses a worker is allowed to set. Approving/rejecting stays with admins.
     */
    public const ALLOWED_STATUSES = [
        Appointment::STATUS_IN_PROGRESS,
        Appointment::STATUS_COMPLETED,
        Appointment::STATUS_CANCELLED,
    ];

    public function index(Request $request): View
    {
        $showAll = $request->boolean('all');

        return view('worker.appointments.index', [
            'appointments' => Appointment::with(['user', 'vehicle', 'services'])
                ->unless($showAll, fn ($query) => $query->whereIn('status', [
                    Appointment::STATUS_APPROVED,
                    Appointment::STATUS_IN_PROGRESS,
                ]))
                ->orderBy('scheduled_at')
                ->paginate(20)
                ->withQueryString(),
            'showAll' => $showAll,
        ]);
    }

    public function show(Appointment $appointment): View
    {
        return view('worker.appointments.show', [
            'appointment' => $appointment->load(['user', 'vehicle', 'services', 'comments.user', 'review']),
        ]);
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::ALLOWED_STATUSES)],
        ]);

        $oldStatus = $appointment->status;

        if ($oldStatus === $validated['status']) {
            return back();
        }

        // Workers cannot reopen finished or rejected jobs.
        abort_if(in_array($oldStatus, [
            Appointment::STATUS_COMPLETED,
            Appointment::STATUS_CANCELLED,
            Appointment::STATUS_REJECTED,
        ]), 403);

        $appointment->update(['status' => $validated['status']]);

        event(new AppointmentStatusChanged($appointment, $oldStatus, $validated['status']));

        return back()->with('status', __('Appointment status updated.'));
    }

    public function storeComment(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $appointment->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return back()->with('status', __('Comment added.'));
    }
}
