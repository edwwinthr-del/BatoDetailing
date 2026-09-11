<x-layout>
    <x-slot:title>{{ __('Register') }}</x-slot:title>

    <x-card class="mx-auto max-w-lg p-8">
        <h1 class="text-2xl font-bold">{{ __('Create an account') }}</h1>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="first_name" class="block text-sm font-medium">{{ __('First name') }}</label>
                    <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required autofocus
                           class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                    <x-input-error for="first_name" />
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium">{{ __('Last name') }}</label>
                    <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required
                           class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                    <x-input-error for="last_name" />
                </div>
            </div>

            <div>
                <label for="username" class="block text-sm font-medium">{{ __('Username') }}</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                <x-input-error for="username" />
            </div>

            <div>
                <label for="email" class="block text-sm font-medium">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                <x-input-error for="email" />
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium">{{ __('Phone') }}</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                <x-input-error for="phone" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="block text-sm font-medium">{{ __('Password') }}</label>
                    <input id="password" name="password" type="password" required
                           class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                    <x-input-error for="password" />
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium">{{ __('Confirm password') }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="promo_emails" value="1" @checked(old('promo_emails')) class="rounded border-gray-300">
                {{ __('I want to receive promotional emails') }}
            </label>

            <button type="submit" class="w-full rounded-md bg-gray-900 dark:bg-amber-500 px-4 py-2 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                {{ __('Register') }}
            </button>
        </form>

        <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Already registered?') }} <a href="{{ route('login') }}" class="text-amber-600 hover:underline">{{ __('Log in') }}</a>
        </p>
    </x-card>
</x-layout>
