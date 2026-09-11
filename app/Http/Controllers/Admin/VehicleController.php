<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.vehicles.index', [
            'vehicles' => Vehicle::with('user')
                ->when($request->filled('search'), function ($query) use ($request): void {
                    $search = '%'.$request->string('search').'%';
                    $query->where(fn ($q) => $q
                        ->where('brand', 'like', $search)
                        ->orWhere('model', 'like', $search)
                        ->orWhere('license_plate', 'like', $search));
                })
                ->orderByDesc('id')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('admin.vehicles.edit', ['vehicle' => $vehicle]);
    }

    public function update(VehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            if ($vehicle->image_path) {
                Storage::disk('public')->delete($vehicle->image_path);
            }

            $data['image_path'] = $request->file('image')->store('vehicles', 'public');
        }

        $vehicle->update($data);

        return redirect()->route('admin.vehicles.index')->with('status', __('Vehicle updated.'));
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        if ($vehicle->image_path) {
            Storage::disk('public')->delete($vehicle->image_path);
        }

        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')->with('status', __('Vehicle removed.'));
    }
}
