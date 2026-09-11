<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function edit(SettingsService $settings): View
    {
        return view('admin.pricing.edit', [
            'types' => Vehicle::TYPES,
            'modifiers' => collect(Vehicle::TYPES)
                ->mapWithKeys(fn (string $type) => [$type => $settings->modifierFor($type)]),
            'businessHours' => $settings->businessHours(),
        ]);
    }

    public function update(Request $request, SettingsService $settings): RedirectResponse
    {
        $validated = $request->validate([
            'modifiers' => ['required', 'array'],
            'modifiers.*' => ['required', 'numeric', 'min:0', 'max:9999'],
            'open' => ['required', 'integer', 'between:0,23'],
            'close' => ['required', 'integer', 'between:1,24', 'gt:open'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['integer', 'between:1,7'],
        ]);

        $values = [];

        foreach (Vehicle::TYPES as $type) {
            if (isset($validated['modifiers'][$type])) {
                $values['modifiers.'.$type] = (float) $validated['modifiers'][$type];
            }
        }

        $values['business_hours.open'] = (int) $validated['open'];
        $values['business_hours.close'] = (int) $validated['close'];
        $values['business_hours.days'] = array_map('intval', $validated['days']);

        $settings->setMany($values);

        return back()->with('status', __('Pricing settings updated.'));
    }
}
