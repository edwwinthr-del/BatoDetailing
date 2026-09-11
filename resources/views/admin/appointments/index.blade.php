<x-layout>
    <x-slot:title>{{ __('Appointments') }}</x-slot:title>

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <h1 class="text-2xl font-bold">{{ __('Appointments') }}</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.appointments.calendar') }}" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-sm text-white">{{ __('Calendar view') }}</a>
        </div>
    </div>

    <form method="GET" class="mt-4 flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by customer...') }}"
               class="rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-1.5 text-sm">
        <select name="status" class="rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-1.5 text-sm">
            <option value="">{{ __('All statuses') }}</option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ __(ucfirst(str_replace('_', ' ', $status))) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-sm text-white">{{ __('Filter') }}</button>
    </form>

    <x-card class="mt-6 overflow-x-auto p-0">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-left text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">{{ __('When') }}</th>
                    <th class="px-4 py-3">{{ __('Customer') }}</th>
                    <th class="px-4 py-3">{{ __('Vehicle') }}</th>
                    <th class="px-4 py-3">{{ __('Total') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($appointments as $appointment)
                    <tr>
                        <td class="px-4 py-3">{{ $appointment->scheduled_at->format('d.m.Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $appointment->user->name }}</td>
                        <td class="px-4 py-3">{{ $appointment->vehicle->displayName() }}</td>
                        <td class="px-4 py-3 font-medium">{{ number_format((float) $appointment->total, 2) }}€</td>
                        <td class="px-4 py-3"><x-status-badge :status="$appointment->status" /></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.appointments.show', $appointment) }}" class="text-amber-600 hover:underline">{{ __('Manage') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">{{ $appointments->links() }}</div>
</x-layout>
