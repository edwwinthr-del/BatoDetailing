@csrf

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="brand" class="block text-sm font-medium">{{ __('Brand') }}</label>
        <input id="brand" name="brand" type="text" value="{{ old('brand', $vehicle->brand ?? '') }}" required
               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
        <x-input-error for="brand" />
    </div>
    <div>
        <label for="model" class="block text-sm font-medium">{{ __('Model') }}</label>
        <input id="model" name="model" type="text" value="{{ old('model', $vehicle->model ?? '') }}" required
               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
        <x-input-error for="model" />
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="year" class="block text-sm font-medium">{{ __('Year') }}</label>
        <input id="year" name="year" type="number" min="1950" max="{{ now()->year + 1 }}" value="{{ old('year', $vehicle->year ?? '') }}"
               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
        <x-input-error for="year" />
    </div>
    <div>
        <label for="type" class="block text-sm font-medium">{{ __('Type') }}</label>
        <select id="type" name="type" required class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
            @foreach (\App\Models\Vehicle::TYPES as $type)
                <option value="{{ $type }}" @selected(old('type', $vehicle->type ?? '') === $type)>{{ __(ucfirst($type)) }}</option>
            @endforeach
        </select>
        <x-input-error for="type" />
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="license_plate" class="block text-sm font-medium">{{ __('License plate') }}</label>
        <input id="license_plate" name="license_plate" type="text" value="{{ old('license_plate', $vehicle->license_plate ?? '') }}" required
               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
        <x-input-error for="license_plate" />
    </div>
    <div>
        <label for="color" class="block text-sm font-medium">{{ __('Color') }}</label>
        <input id="color" name="color" type="text" value="{{ old('color', $vehicle->color ?? '') }}"
               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
        <x-input-error for="color" />
    </div>
</div>

<div>
    <label for="image" class="block text-sm font-medium">{{ __('Photo') }}</label>
    <input id="image" name="image" type="file" accept="image/*" class="mt-1 w-full text-sm">
    <x-input-error for="image" />
</div>

<div>
    <label for="notes" class="block text-sm font-medium">{{ __('Notes') }}</label>
    <textarea id="notes" name="notes" rows="3"
              class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">{{ old('notes', $vehicle->notes ?? '') }}</textarea>
    <x-input-error for="notes" />
</div>
