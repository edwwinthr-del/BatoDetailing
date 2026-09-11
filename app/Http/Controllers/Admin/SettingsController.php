<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(SettingsService $settings): View
    {
        return view('admin.settings.edit', [
            'settings' => [
                'company.name' => $settings->get('company.name'),
                'company.email' => $settings->get('company.email'),
                'company.phone' => $settings->get('company.phone'),
                'company.address' => $settings->get('company.address'),
                'theme.default' => $settings->get('theme.default'),
                'loyalty.points_per_euro' => $settings->get('loyalty.points_per_euro'),
                'loyalty.redeem_value' => $settings->get('loyalty.redeem_value'),
                'loyalty.min_redeem' => $settings->get('loyalty.min_redeem'),
            ],
        ]);
    }

    public function update(Request $request, SettingsService $settings): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'email', 'max:255'],
            'company_phone' => ['required', 'string', 'max:30'],
            'company_address' => ['required', 'string', 'max:500'],
            'default_theme' => ['required', Rule::in(['light', 'dark'])],
            'points_per_euro' => ['required', 'numeric', 'min:0', 'max:100'],
            'redeem_value' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_redeem' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);

        $settings->setMany([
            'company.name' => $validated['company_name'],
            'company.email' => $validated['company_email'],
            'company.phone' => $validated['company_phone'],
            'company.address' => $validated['company_address'],
            'theme.default' => $validated['default_theme'],
            'loyalty.points_per_euro' => (float) $validated['points_per_euro'],
            'loyalty.redeem_value' => (float) $validated['redeem_value'],
            'loyalty.min_redeem' => (int) $validated['min_redeem'],
        ]);

        return back()->with('status', __('Settings updated.'));
    }
}
