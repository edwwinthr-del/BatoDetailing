<x-layout>
    <x-slot:title>{{ __('Profile') }}</x-slot:title>

    <x-card class="mx-auto max-w-lg p-8">
        <h1 class="text-2xl font-bold">{{ __('My profile') }}</h1>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-4">
                @if ($user->avatar_path)
                    <img src="{{ asset('storage/'.$user->avatar_path) }}" alt="" class="size-16 rounded-full object-cover">
                @else
                    <div class="size-16 rounded-full bg-amber-500 flex items-center justify-center text-xl font-bold text-gray-900">
                        {{ mb_substr($user->first_name, 0, 1) }}{{ mb_substr($user->last_name, 0, 1) }}
                    </div>
                @endif
                <div class="flex-1">
                    <label for="avatar" class="block text-sm font-medium">{{ __('Avatar') }}</label>
                    <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 w-full text-sm">
                    <x-input-error for="avatar" />
                </div>
            </div>

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
                <label for="phone" class="block text-sm font-medium">{{ __('Phone') }}</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" required
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                <x-input-error for="phone" />
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="promo_emails" value="1" @checked(old('promo_emails', $user->promo_emails)) class="rounded border-gray-300">
                {{ __('I want to receive promotional emails') }}
            </label>

            <button type="submit" class="w-full rounded-md bg-gray-900 dark:bg-amber-500 px-4 py-2 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                {{ __('Save changes') }}
            </button>
        </form>
    </x-card>
</x-layout>
