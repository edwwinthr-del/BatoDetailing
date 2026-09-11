<x-layout>
    <x-slot:title>{{ __('Users') }}</x-slot:title>

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <h1 class="text-2xl font-bold">{{ __('Users') }}</h1>
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search users...') }}"
                   class="rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-1.5 text-sm">
            <button type="submit" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-sm text-white">{{ __('Search') }}</button>
        </form>
    </div>

    <x-card class="mt-6 overflow-x-auto p-0">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-left text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Username') }}</th>
                    <th class="px-4 py-3">{{ __('Email') }}</th>
                    <th class="px-4 py-3">{{ __('Role') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($users as $user)
                    <tr class="{{ $user->is_blocked ? 'opacity-60' : '' }}">
                        <td class="px-4 py-3 text-gray-400">{{ $user->id }}</td>
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->username }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $user->role?->name === 'admin' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                {{ $user->role?->name ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($user->is_blocked)
                                <span class="text-red-600 text-xs font-semibold">{{ __('Blocked') }}</span>
                            @else
                                <span class="text-green-600 text-xs font-semibold">{{ __('Active') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-amber-600 hover:underline">{{ __('Edit') }}</a>
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.toggle-block', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="{{ $user->is_blocked ? 'text-green-600' : 'text-red-600' }} hover:underline">
                                            {{ $user->is_blocked ? __('Unblock') : __('Block') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">{{ $users->links() }}</div>
</x-layout>
