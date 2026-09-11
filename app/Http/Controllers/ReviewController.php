<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Appointment $appointment): RedirectResponse
    {
        Gate::authorize('review', $appointment);

        $appointment->review()->create([
            'user_id' => $request->user()->id,
            ...$request->validated(),
        ]);

        return redirect()->route('appointments.show', $appointment)->with('status', __('Thank you for your review!'));
    }
}
