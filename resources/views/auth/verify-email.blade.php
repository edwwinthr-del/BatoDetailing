<x-layout>
    <x-slot:title>{{ __('Verify email') }}</x-slot:title>

    <x-card class="mx-auto max-w-md p-8 text-center">
        <h1 class="text-2xl font-bold">{{ __('Verify your email') }}</h1>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
            {{ __('We sent a verification link to your email address. Please click it to activate your account.') }}
        </p>

        <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
            @csrf
            <button type="submit" class="rounded-md bg-gray-900 dark:bg-amber-500 px-5 py-2.5 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                {{ __('Resend verification email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:underline">{{ __('Log out') }}</button>
        </form>
    </x-card>
</x-layout>
