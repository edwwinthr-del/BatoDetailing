<x-layout>
    <x-slot:title>{{ __('Edit user') }}</x-slot:title>

    <x-card class="mx-auto max-w-lg p-8">
        <h1 class="text-2xl font-bold">{{ __('Edit user') }} #{{ $user->id }}</h1>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="first_name" class="block text-sm font-medium">{{ __('First name') }}</label>
                    <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $user->first_name) }}" required
                           class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                    <x-input-error for="first_name" />
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium">{{ __('Last name') }}</label>
                    <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name) }}" required
                           class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                    <x-input-error for="last_name" />
                </div>
            </div>

            <div>
                <label for="username" class="block text-sm font-medium">{{ __('Username') }}</label>
                <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                <x-input-error for="username" />
            </div>

            <div>
                <label for="email" class="block text-sm font-medium">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                <x-input-error for="email" />
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium">{{ __('Phone') }}</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" required
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                <x-input-error for="phone" />
            </div>

            <div>
                <label for="role_id" class="block text-sm font-medium">{{ __('Role') }}</label>
                <select id="role_id" name="role_id" required class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
                <x-input-error for="role_id" />
            </div>

            <button type="submit" class="w-full rounded-md bg-gray-900 dark:bg-amber-500 px-4 py-2 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                {{ __('Save changes') }}
            </button>
        </form>
    </x-card>
</x-layout>
