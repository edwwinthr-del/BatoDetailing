<x-layout>
    <x-slot:title>{{ __('Forgot password') }}</x-slot:title>

    <x-card class="mx-auto max-w-md p-8">
        <h1 class="text-2xl font-bold">{{ __('Forgot password') }}</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Enter your email and we will send you a password reset link.') }}</p>

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                <x-input-error for="email" />
            </div>

            <button type="submit" class="w-full rounded-md bg-gray-900 dark:bg-amber-500 px-4 py-2 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                {{ __('Send reset link') }}
            </button>
        </form>
    </x-card>
</x-layout>
