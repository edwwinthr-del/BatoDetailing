<x-layout>
    <x-slot:title>{{ __('Work queue') }}</x-slot:title>

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <h1 class="text-2xl font-bold">{{ __('Work queue') }}</h1>
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('worker.appointments.index') }}"
               class="rounded-md px-3 py-1.5 {{ $showAll ? 'border border-gray-300 dark:border-gray-700' : 'bg-gray-900 dark:bg-gray-800 text-white' }}">
                {{ __('Active jobs') }}
            </a>
            <a href="{{ route('worker.appointments.index', ['all' => 1]) }}"
               class="rounded-md px-3 py-1.5 {{ $showAll ? 'bg-gray-900 dark:bg-gray-800 text-white' : 'border border-gray-300 dark:border-gray-700' }}">
                {{ __('All appointments') }}
            </a>
        </div>
    </div>

    @if ($appointments->isEmpty())
        <p class="mt-6 text-gray-600 dark:text-gray-400">{{ __('No jobs in the queue right now.') }}</p>
    @else
        <x-card class="mt-6 overflow-x-auto p-0">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 text-left text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">{{ __('When') }}</th>
                        <th class="px-4 py-3">{{ __('Customer') }}</th>
                        <th class="px-4 py-3">{{ __('Vehicle') }}</th>
                        <th class="px-4 py-3">{{ __('Services') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($appointments as $appointment)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $appointment->scheduled_at->format('d.m.Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $appointment->user->name }}</td>
                            <td class="px-4 py-3">{{ $appointment->vehicle->displayName() }} ({{ __(ucfirst($appointment->vehicle->type)) }})</td>
                            <td class="px-4 py-3">{{ $appointment->services->pluck('name')->join(', ') }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$appointment->status" /></td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('worker.appointments.show', $appointment) }}" class="text-amber-600 hover:underline">{{ __('Open') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>

        <div class="mt-4">{{ $appointments->links() }}</div>
    @endif
</x-layout>
