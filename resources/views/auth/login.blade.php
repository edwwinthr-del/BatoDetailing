<x-layout>
    <x-slot:title>{{ __('Log in') }}</x-slot:title>

    <x-card class="mx-auto max-w-md p-8">
        <h1 class="text-2xl font-bold">{{ __('Log in') }}</h1>

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                <x-input-error for="email" />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" required
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                <x-input-error for="password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="remember" value="1" class="rounded border-gray-300">
                    {{ __('Remember me') }}
                </label>
                <a href="{{ route('password.request') }}" class="text-sm text-amber-600 hover:underline">{{ __('Forgot password?') }}</a>
            </div>

            <button type="submit" class="w-full rounded-md bg-gray-900 dark:bg-amber-500 px-4 py-2 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                {{ __('Log in') }}
            </button>
        </form>

        <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('No account?') }} <a href="{{ route('register') }}" class="text-amber-600 hover:underline">{{ __('Register') }}</a>
        </p>
    </x-card>
</x-layout>
