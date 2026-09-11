<x-layout>
    <x-slot:title>{{ __('Invoices') }}</x-slot:title>

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <h1 class="text-2xl font-bold">{{ __('Invoices') }}</h1>
        <a href="{{ route('admin.invoices.export') }}" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-sm text-white">{{ __('Export CSV') }}</a>
    </div>

    <form method="GET" class="mt-4 flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by number or customer...') }}"
               class="rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-1.5 text-sm">
        <select name="status" class="rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-1.5 text-sm">
            <option value="">{{ __('All statuses') }}</option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ __(ucfirst($status)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-sm text-white">{{ __('Filter') }}</button>
    </form>

    <x-card class="mt-6 overflow-x-auto p-0">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-left text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">{{ __('Number') }}</th>
                    <th class="px-4 py-3">{{ __('Customer') }}</th>
                    <th class="px-4 py-3">{{ __('Issued') }}</th>
                    <th class="px-4 py-3">{{ __('Total') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($invoices as $invoice)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $invoice->number }}</td>
                        <td class="px-4 py-3">{{ $invoice->user->name }}</td>
                        <td class="px-4 py-3">{{ $invoice->issued_at->format('d.m.Y') }}</td>
                        <td class="px-4 py-3 font-medium">{{ number_format((float) $invoice->total, 2) }}€</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.invoices.status', $invoice) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-2 py-1 text-xs">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}" @selected($invoice->status === $status)>{{ __(ucfirst($status)) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="text-xs text-amber-600 hover:underline">{{ __('Set') }}</button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3 text-xs">
                                <form method="POST" action="{{ route('admin.invoices.resend', $invoice) }}">
                                    @csrf
                                    <button type="submit" class="text-amber-600 hover:underline">{{ __('Resend email') }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.invoices.regenerate', $invoice) }}">
                                    @csrf
                                    <button type="submit" class="text-gray-500 hover:underline">{{ __('Regenerate PDF') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">{{ $invoices->links() }}</div>
</x-layout>
