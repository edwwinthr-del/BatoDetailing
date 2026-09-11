<x-layout>
    <x-slot:title>{{ __('Reports') }}</x-slot:title>

    <h1 class="text-2xl font-bold">{{ __('Reports') }}</h1>

    <form method="GET" class="mt-4 flex flex-wrap items-end gap-3">
        <div>
            <label for="from" class="block text-sm font-medium">{{ __('From') }}</label>
            <input id="from" name="from" type="date" value="{{ request('from', $from->format('Y-m-d')) }}"
                   class="mt-1 rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-1.5 text-sm">
        </div>
        <div>
            <label for="to" class="block text-sm font-medium">{{ __('To') }}</label>
            <input id="to" name="to" type="date" value="{{ request('to', $to->format('Y-m-d')) }}"
                   class="mt-1 rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-1.5 text-sm">
        </div>
        <button type="submit" class="rounded-md bg-gray-900 dark:bg-gray-800 px-4 py-1.5 text-sm text-white">{{ __('Apply') }}</button>
        <a href="{{ route('admin.reports.export.csv', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
           class="rounded-md border border-gray-300 dark:border-gray-700 px-4 py-1.5 text-sm">{{ __('Export CSV') }}</a>
        <a href="{{ route('admin.reports.export.pdf', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
           class="rounded-md border border-gray-300 dark:border-gray-700 px-4 py-1.5 text-sm">{{ __('Export PDF') }}</a>
    </form>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Revenue') }}</p>
            <p class="mt-1 text-3xl font-bold text-amber-500">{{ number_format($report['revenue'], 2) }}€</p>
            <p class="text-xs text-gray-400">{{ $report['invoiceCount'] }} {{ __('invoices') }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Appointments') }}</p>
            <p class="mt-1 text-3xl font-bold">{{ $report['appointmentCount'] }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Completed') }}</p>
            <p class="mt-1 text-3xl font-bold text-green-500">{{ $report['completedCount'] }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Satisfaction') }}</p>
            <p class="mt-1 text-3xl font-bold">{{ $report['averageRating'] ?: '—' }}<span class="text-base text-gray-400">/5</span></p>
            <p class="text-xs text-gray-400">{{ $report['reviewCount'] }} {{ __('reviews') }}</p>
        </x-card>
    </div>

    <h2 class="mt-8 text-xl font-semibold">{{ __('Service popularity') }}</h2>
    <x-card class="mt-3 overflow-x-auto p-0">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-left text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">{{ __('Service') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Bookings') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Revenue') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($report['servicePopularity'] as $row)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $row->name }}</td>
                        <td class="px-4 py-3 text-right">{{ $row->bookings }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format((float) $row->revenue, 2) }}€</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">{{ __('No data for this period.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
</x-layout>
