<x-layout>
    <x-slot:title>{{ __('Add vehicle') }}</x-slot:title>

    <x-card class="mx-auto max-w-lg p-8">
        <h1 class="text-2xl font-bold">{{ __('Add a vehicle') }}</h1>

        <form method="POST" action="{{ route('vehicles.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @include('vehicles._form')
            <button type="submit" class="w-full rounded-md bg-gray-900 dark:bg-amber-500 px-4 py-2 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                {{ __('Save vehicle') }}
            </button>
        </form>
    </x-card>
</x-layout>
