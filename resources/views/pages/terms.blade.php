<x-layout>
    <x-slot:title>{{ __('Terms of Service') }}</x-slot:title>

    <x-card class="mx-auto max-w-3xl">
        <h1 class="text-2xl font-bold">{{ __('Terms of Service') }}</h1>
        <div class="mt-4 space-y-4 text-sm text-gray-700 dark:text-gray-300">
            <p>{{ __('By using the BatoDetailing website and services you agree to these terms.') }}</p>
            <h2 class="font-semibold text-base">{{ __('Bookings') }}</h2>
            <p>{{ __('Appointments are requests until approved by our staff. Prices shown at booking are final and include your vehicle type surcharge. You may cancel a pending or approved appointment free of charge.') }}</p>
            <h2 class="font-semibold text-base">{{ __('Payments & invoices') }}</h2>
            <p>{{ __('An invoice is issued automatically when your appointment is completed and sent to your email address.') }}</p>
            <h2 class="font-semibold text-base">{{ __('Loyalty points') }}</h2>
            <p>{{ __('Loyalty points are awarded on completed appointments and can be redeemed for discounts on future bookings. Points have no cash value.') }}</p>
            <h2 class="font-semibold text-base">{{ __('Liability') }}</h2>
            <p>{{ __('We take the utmost care with every vehicle. Pre-existing damage must be reported at drop-off.') }}</p>
        </div>
    </x-card>
</x-layout>
