<x-layout>
    <x-slot:title>{{ __('All Vehicles') }}</x-slot:title>

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <h1 class="text-2xl font-bold">{{ __('All Vehicles') }}</h1>
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search vehicles...') }}"
                   class="rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-1.5 text-sm">
            <button type="submit" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-sm text-white">{{ __('Search') }}</button>
        </form>
    </div>

    <x-card class="mt-6 overflow-x-auto p-0">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-left text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">{{ __('Vehicle') }}</th>
                    <th class="px-4 py-3">{{ __('Type') }}</th>
                    <th class="px-4 py-3">{{ __('Plate') }}</th>
                    <th class="px-4 py-3">{{ __('Owner') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($vehicles as $vehicle)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $vehicle->displayName() }}</td>
                        <td class="px-4 py-3">{{ __(ucfirst($vehicle->type)) }}</td>
                        <td class="px-4 py-3">{{ $vehicle->license_plate }}</td>
                        <td class="px-4 py-3">{{ $vehicle->user->name }} <span class="text-gray-400">({{ $vehicle->user->email }})</span></td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="text-amber-600 hover:underline">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}" onsubmit="return confirm(@js(__('Remove this vehicle?')));">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">{{ $vehicles->links() }}</div>
</x-layout>
