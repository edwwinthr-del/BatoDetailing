<x-layout>
    <x-slot:title>{{ __('Privacy Policy') }}</x-slot:title>

    <x-card class="mx-auto max-w-3xl prose-sm">
        <h1 class="text-2xl font-bold">{{ __('Privacy Policy') }}</h1>
        <div class="mt-4 space-y-4 text-sm text-gray-700 dark:text-gray-300">
            <p>{{ __('BatoDetailing respects your privacy. This policy explains what data we collect and how we use it.') }}</p>
            <h2 class="font-semibold text-base">{{ __('Data we collect') }}</h2>
            <p>{{ __('We store the account details you provide (name, username, email, phone), your vehicles, your appointments, invoices, reviews and loyalty point history. Vehicle photos and avatars you upload are stored on our servers.') }}</p>
            <h2 class="font-semibold text-base">{{ __('How we use your data') }}</h2>
            <p>{{ __('Your data is used solely to provide our detailing services: managing bookings, issuing invoices, and sending transactional emails. Promotional emails are sent only if you opt in, and you can opt out at any time from your profile.') }}</p>
            <h2 class="font-semibold text-base">{{ __('Your rights') }}</h2>
            <p>{{ __('You may request a copy or deletion of your personal data at any time by contacting us.') }}</p>
        </div>
    </x-card>
</x-layout>
