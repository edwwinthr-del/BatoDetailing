<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendCampaign;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function create(): View
    {
        return view('admin.campaigns.create', [
            'recipientCount' => User::where('promo_emails', true)->where('is_blocked', false)->count(),
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        SendCampaign::dispatch($validated['subject'], $validated['body']);

        return back()->with('status', __('Campaign queued. Emails will be sent to all opted-in users.'));
    }
}
