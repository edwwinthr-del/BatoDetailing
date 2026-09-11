<x-layout>
    <x-slot:title>{{ __('My Appointments') }}</x-slot:title>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ __('My Appointments') }}</h1>
        <a href="{{ route('appointments.create') }}" class="rounded-md bg-amber-500 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-amber-400 transition">{{ __('New booking') }}</a>
    </div>

    @if ($appointments->isEmpty())
        <p class="mt-6 text-gray-600 dark:text-gray-400">{{ __('You have no appointments yet.') }}</p>
    @else
        <x-card class="mt-6 overflow-x-auto p-0">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 text-left text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">{{ __('When') }}</th>
                        <th class="px-4 py-3">{{ __('Vehicle') }}</th>
                        <th class="px-4 py-3">{{ __('Services') }}</th>
                        <th class="px-4 py-3">{{ __('Total') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3">{{ __('Invoice') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($appointments as $appointment)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('appointments.show', $appointment) }}" class="text-amber-600 hover:underline">
                                    {{ $appointment->scheduled_at->format('d.m.Y H:i') }}
                                </a>
                            </td>
                            <td class="px-4 py-3">{{ $appointment->vehicle->displayName() }}</td>
                            <td class="px-4 py-3">{{ $appointment->services->pluck('name')->join(', ') }}</td>
                            <td class="px-4 py-3 font-medium">{{ number_format((float) $appointment->total, 2) }}€</td>
                            <td class="px-4 py-3"><x-status-badge :status="$appointment->status" /></td>
                            <td class="px-4 py-3">
                                @if ($appointment->invoice)
                                    <a href="{{ route('invoices.show', $appointment->invoice) }}" class="text-amber-600 hover:underline">{{ $appointment->invoice->number }}</a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>

        <div class="mt-4">{{ $appointments->links() }}</div>
    @endif
</x-layout>
