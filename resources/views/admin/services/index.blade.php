<x-layout>
    <x-slot:title>{{ __('Services') }}</x-slot:title>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ __('Services') }}</h1>
        <a href="{{ route('admin.services.create') }}" class="rounded-md bg-amber-500 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-amber-400 transition">{{ __('Add service') }}</a>
    </div>

    <x-card class="mt-6 overflow-x-auto p-0">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-left text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Base price') }}</th>
                    <th class="px-4 py-3">{{ __('Duration') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($services as $service)
                    <tr class="{{ $service->is_active ? '' : 'opacity-60' }}">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $service->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $service->description }}</p>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ number_format((float) $service->base_price, 2) }}€</td>
                        <td class="px-4 py-3">{{ $service->duration_minutes }} {{ __('min') }}</td>
                        <td class="px-4 py-3">
                            @if ($service->is_active)
                                <span class="text-green-600 text-xs font-semibold">{{ __('Active') }}</span>
                            @else
                                <span class="text-gray-400 text-xs font-semibold">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('admin.services.edit', $service) }}" class="text-amber-600 hover:underline">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('admin.services.toggle', $service) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="{{ $service->is_active ? 'text-red-600' : 'text-green-600' }} hover:underline">
                                        {{ $service->is_active ? __('Deactivate') : __('Activate') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</x-layout>
