<x-layout>
    <x-slot:title>{{ __('Pricing settings') }}</x-slot:title>

    <x-card class="mx-auto max-w-lg p-8">
        <h1 class="text-2xl font-bold">{{ __('Pricing settings') }}</h1>

        <form method="POST" action="{{ route('admin.pricing.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h2 class="font-semibold">{{ __('Vehicle type surcharges') }} (€)</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Added on top of the service base price depending on the vehicle type.') }}</p>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    @foreach ($types as $type)
                        <div>
                            <label for="modifier-{{ $type }}" class="block text-sm font-medium">{{ __(ucfirst($type)) }}</label>
                            <input id="modifier-{{ $type }}" name="modifiers[{{ $type }}]" type="number" step="0.01" min="0"
                                   value="{{ old('modifiers.'.$type, $modifiers[$type]) }}" required
                                   class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                        </div>
                    @endforeach
                </div>
                <x-input-error for="modifiers.*" />
            </div>

            <div>
                <h2 class="font-semibold">{{ __('Business hours') }}</h2>
                <div class="mt-3 grid gap-3 grid-cols-2">
                    <div>
                        <label for="open" class="block text-sm font-medium">{{ __('Opens at (hour)') }}</label>
                        <input id="open" name="open" type="number" min="0" max="23" value="{{ old('open', $businessHours['open']) }}" required
                               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                        <x-input-error for="open" />
                    </div>
                    <div>
                        <label for="close" class="block text-sm font-medium">{{ __('Closes at (hour)') }}</label>
                        <input id="close" name="close" type="number" min="1" max="24" value="{{ old('close', $businessHours['close']) }}" required
                               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                        <x-input-error for="close" />
                    </div>
                </div>
                <div class="mt-3">
                    <span class="block text-sm font-medium">{{ __('Working days') }}</span>
                    <div class="mt-2 flex flex-wrap gap-3">
                        @foreach ([1 => __('Mon'), 2 => __('Tue'), 3 => __('Wed'), 4 => __('Thu'), 5 => __('Fri'), 6 => __('Sat'), 7 => __('Sun')] as $dayNumber => $dayLabel)
                            <label class="flex items-center gap-1.5 text-sm">
                                <input type="checkbox" name="days[]" value="{{ $dayNumber }}"
                                       @checked(in_array($dayNumber, old('days', $businessHours['days'])))
                                       class="rounded border-gray-300">
                                {{ $dayLabel }}
                            </label>
                        @endforeach
                    </div>
                    <x-input-error for="days" />
                </div>
            </div>

            <button type="submit" class="w-full rounded-md bg-gray-900 dark:bg-amber-500 px-4 py-2 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                {{ __('Save settings') }}
            </button>
        </form>
    </x-card>
</x-layout>
