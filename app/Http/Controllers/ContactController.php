<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request, SettingsService $settings): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        Mail::to($settings->get('company.email'))->queue(new ContactMessage($validated));

        return back()->with('status', __('Thank you! Your message has been sent.'));
    }
}
