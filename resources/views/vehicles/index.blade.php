<x-layout>
    <x-slot:title>{{ __('My Vehicles') }}</x-slot:title>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ __('My Vehicles') }}</h1>
        <a href="{{ route('vehicles.create') }}" class="rounded-md bg-amber-500 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-amber-400 transition">{{ __('Add vehicle') }}</a>
    </div>

    @if ($vehicles->isEmpty())
        <p class="mt-6 text-gray-600 dark:text-gray-400">{{ __("You haven't added any vehicles yet.") }}</p>
    @else
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($vehicles as $vehicle)
                <x-card class="overflow-hidden p-0">
                    @if ($vehicle->image_path)
                        <img src="{{ asset('storage/'.$vehicle->image_path) }}" alt="{{ $vehicle->displayName() }}" class="h-40 w-full object-cover">
                    @else
                        <div class="h-40 w-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center text-5xl">🚘</div>
                    @endif
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="font-semibold">{{ $vehicle->displayName() }}</h2>
                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __(ucfirst($vehicle->type)) }} · {{ $vehicle->license_plate }}@if($vehicle->color) · {{ $vehicle->color }}@endif
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-4 text-sm">
                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="text-amber-600 hover:underline">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" onsubmit="return confirm(@js(__('Remove this vehicle?')));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">{{ __('Remove') }}</button>
                            </form>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</x-layout>
