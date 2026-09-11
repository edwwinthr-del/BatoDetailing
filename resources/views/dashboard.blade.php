<x-layout>
    <x-slot:title>{{ __('Dashboard') }}</x-slot:title>

    <h1 class="text-2xl font-bold">{{ __('Welcome back, :name', ['name' => auth()->user()->first_name]) }}</h1>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total vehicles') }}</p>
            <p class="mt-1 text-3xl font-bold">{{ $vehicleCount }}</p>
            <a href="{{ route('vehicles.index') }}" class="mt-2 inline-block text-sm text-amber-600 hover:underline">{{ __('Manage vehicles') }}</a>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total appointments') }}</p>
            <p class="mt-1 text-3xl font-bold">{{ $appointmentCount }}</p>
            <a href="{{ route('appointments.index') }}" class="mt-2 inline-block text-sm text-amber-600 hover:underline">{{ __('View appointments') }}</a>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Loyalty points') }}</p>
            <p class="mt-1 text-3xl font-bold text-amber-500">{{ $loyaltyBalance }}</p>
            <p class="mt-2 text-xs text-gray-400">{{ __('Redeem them on your next booking') }}</p>
        </x-card>
        <x-card class="flex flex-col justify-between">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Ready for a shine?') }}</p>
            <a href="{{ route('appointments.create') }}" class="mt-2 rounded-md bg-amber-500 px-4 py-2 text-center font-semibold text-gray-900 hover:bg-amber-400 transition">
                {{ __('Book Appointment') }}
            </a>
        </x-card>
    </div>

    <h2 class="mt-10 text-xl font-semibold">{{ __('Upcoming appointment') }}</h2>
    @if ($upcomingAppointment)
        <x-card class="mt-3">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-lg font-semibold">{{ $upcomingAppointment->scheduled_at->format('d.m.Y H:i') }}</p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $upcomingAppointment->vehicle->displayName() }} ·
                        {{ $upcomingAppointment->services->pluck('name')->join(', ') }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <x-status-badge :status="$upcomingAppointment->status" />
                    <a href="{{ route('appointments.show', $upcomingAppointment) }}" class="text-sm text-amber-600 hover:underline">{{ __('Details') }}</a>
                </div>
            </div>
        </x-card>
    @else
        <p class="mt-3 text-gray-600 dark:text-gray-400">{{ __('No upcoming appointments.') }}
            <a href="{{ route('appointments.create') }}" class="text-amber-600 hover:underline">{{ __('Book one now') }}</a>
        </p>
    @endif
</x-layout>
