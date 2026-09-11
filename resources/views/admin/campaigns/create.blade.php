<x-layout>
    <x-slot:title>{{ __('Email campaign') }}</x-slot:title>

    <x-card class="mx-auto max-w-lg p-8">
        <h1 class="text-2xl font-bold">{{ __('Email campaign') }}</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('This will be sent to :count opted-in users via the queue.', ['count' => $recipientCount]) }}
        </p>

        <form method="POST" action="{{ route('admin.campaigns.send') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="subject" class="block text-sm font-medium">{{ __('Subject') }}</label>
                <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required
                       class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                <x-input-error for="subject" />
            </div>

            <div>
                <label for="body" class="block text-sm font-medium">{{ __('Message') }}</label>
                <textarea id="body" name="body" rows="8" required
                          class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">{{ old('body') }}</textarea>
                <x-input-error for="body" />
            </div>

            <button type="submit" class="w-full rounded-md bg-gray-900 dark:bg-amber-500 px-4 py-2 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition"
                    onclick="return confirm(@js(__('Send this campaign to all opted-in users?')));">
                {{ __('Send campaign') }}
            </button>
        </form>
    </x-card>
</x-layout>
