<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(SettingsService $settings): View
    {
        return view('home', [
            'services' => Service::where('is_active', true)->orderBy('base_price')->get(),
            'testimonials' => Review::with('user')
                ->where('rating', '>=', 4)
                ->whereNotNull('comment')
                ->latest()
                ->take(6)
                ->get(),
            'company' => [
                'name' => $settings->get('company.name'),
                'email' => $settings->get('company.email'),
                'phone' => $settings->get('company.phone'),
                'address' => $settings->get('company.address'),
            ],
            'stats' => [
                'completed' => Appointment::where('status', Appointment::STATUS_COMPLETED)->count(),
                'customers' => User::count(),
                'services' => Service::where('is_active', true)->count(),
            ],
        ]);
    }
}
