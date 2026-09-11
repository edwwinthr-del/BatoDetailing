<x-layout>
    <x-slot:title>{{ __('My Invoices') }}</x-slot:title>

    <h1 class="text-2xl font-bold">{{ __('My Invoices') }}</h1>

    @if ($invoices->isEmpty())
        <p class="mt-6 text-gray-600 dark:text-gray-400">{{ __('You have no invoices yet. Invoices appear here after your appointments are completed.') }}</p>
    @else
        <x-card class="mt-6 overflow-x-auto p-0">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 text-left text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">{{ __('Number') }}</th>
                        <th class="px-4 py-3">{{ __('Vehicle') }}</th>
                        <th class="px-4 py-3">{{ __('Issued') }}</th>
                        <th class="px-4 py-3">{{ __('Total') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-amber-600 hover:underline font-medium">{{ $invoice->number }}</a>
                            </td>
                            <td class="px-4 py-3">{{ $invoice->appointment->vehicle->displayName() }}</td>
                            <td class="px-4 py-3">{{ $invoice->issued_at->format('d.m.Y') }}</td>
                            <td class="px-4 py-3 font-medium">{{ number_format((float) $invoice->total, 2) }}€</td>
                            <td class="px-4 py-3"><x-status-badge :status="$invoice->status" /></td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('invoices.download', $invoice) }}" class="text-amber-600 hover:underline">{{ __('Download PDF') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>

        <div class="mt-4">{{ $invoices->links() }}</div>
    @endif
</x-layout>
