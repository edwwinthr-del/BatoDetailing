<x-layout>
    <x-slot:title>{{ __('Reset password') }}</x-slot:title>

    <x-card class="mx-auto max-w-md p-8">
        <h1 class="text-2xl font-bold">{{ __('Reset password') }}</h1>

        <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-medium">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                <x-input-error for="email" />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium">{{ __('New password') }}</label>
                <input id="password" name="password" type="password" required
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                <x-input-error for="password" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium">{{ __('Confirm password') }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
            </div>

            <button type="submit" class="w-full rounded-md bg-gray-900 dark:bg-amber-500 px-4 py-2 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                {{ __('Reset password') }}
            </button>
        </form>
    </x-card>
</x-layout>
