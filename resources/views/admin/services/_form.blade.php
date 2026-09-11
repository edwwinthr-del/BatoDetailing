@csrf

<div>
    <label for="name" class="block text-sm font-medium">{{ __('Name') }}</label>
    <input id="name" name="name" type="text" value="{{ old('name', $service->name ?? '') }}" required
           class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
    <x-input-error for="name" />
</div>

<div>
    <label for="description" class="block text-sm font-medium">{{ __('Description') }}</label>
    <textarea id="description" name="description" rows="3"
              class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">{{ old('description', $service->description ?? '') }}</textarea>
    <x-input-error for="description" />
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="base_price" class="block text-sm font-medium">{{ __('Base price') }} (€)</label>
        <input id="base_price" name="base_price" type="number" step="0.01" min="0" value="{{ old('base_price', $service->base_price ?? '') }}" required
               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
        <x-input-error for="base_price" />
    </div>
    <div>
        <label for="duration_minutes" class="block text-sm font-medium">{{ __('Duration (minutes)') }}</label>
        <input id="duration_minutes" name="duration_minutes" type="number" min="15" max="1440" value="{{ old('duration_minutes', $service->duration_minutes ?? 60) }}" required
               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
        <x-input-error for="duration_minutes" />
    </div>
</div>

<label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active ?? true)) class="rounded border-gray-300">
    {{ __('Active') }}
</label>
