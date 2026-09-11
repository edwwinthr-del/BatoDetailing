<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('services.index', [
            'services' => Service::where('is_active', true)
                ->with(['packages' => fn ($query) => $query->where('is_active', true)->orderBy('price')])
                ->orderBy('name')
                ->get(),
        ]);
    }
}
