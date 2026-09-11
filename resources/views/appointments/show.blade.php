<x-layout>
    <x-slot:title>{{ __('Appointment') }} #{{ $appointment->id }}</x-slot:title>

    <div class="mx-auto max-w-2xl">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ __('Appointment') }} #{{ $appointment->id }}</h1>
            <x-status-badge :status="$appointment->status" />
        </div>

        <x-card class="mt-6">
            <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Scheduled') }}</dt>
                    <dd class="mt-0.5 font-medium">{{ $appointment->scheduled_at->format('d.m.Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Vehicle') }}</dt>
                    <dd class="mt-0.5 font-medium">{{ $appointment->vehicle->displayName() }} ({{ $appointment->vehicle->license_plate }})</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Services') }}</dt>
                    <dd class="mt-1 space-y-1">
                        @foreach ($appointment->services as $service)
                            <p class="flex justify-between"><span>{{ $service->name }}</span> <span>{{ number_format((float) $service->pivot->price, 2) }}€</span></p>
                        @endforeach
                    </dd>
                </div>
                @if ($appointment->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Notes') }}</dt>
                        <dd class="mt-0.5">{{ $appointment->notes }}</dd>
                    </div>
                @endif
            </dl>

            <div class="mt-4 border-t border-gray-100 dark:border-gray-800 pt-4 text-sm space-y-1">
                <p class="flex justify-between"><span>{{ __('Services subtotal') }}</span> <span>{{ number_format((float) $appointment->subtotal, 2) }}€</span></p>
                <p class="flex justify-between"><span>{{ __('Vehicle type surcharge') }}</span> <span>{{ number_format((float) $appointment->type_modifier, 2) }}€</span></p>
                @if ((float) $appointment->discount > 0)
                    <p class="flex justify-between"><span>{{ __('Loyalty discount') }} ({{ $appointment->points_redeemed }} {{ __('points') }})</span> <span>-{{ number_format((float) $appointment->discount, 2) }}€</span></p>
                @endif
                <p class="flex justify-between font-bold text-base"><span>{{ __('Total') }}</span> <span class="text-amber-600">{{ number_format((float) $appointment->total, 2) }}€</span></p>
            </div>
        </x-card>

        @if ($appointment->invoice)
            <x-card class="mt-6">
                <h2 class="text-lg font-semibold">{{ __('Invoice') }}</h2>
                <div class="mt-2 flex items-center justify-between text-sm">
                    <p>{{ $appointment->invoice->number }} · <x-status-badge :status="$appointment->invoice->status" /></p>
                    <a href="{{ route('invoices.download', $appointment->invoice) }}" class="text-amber-600 hover:underline">{{ __('Download PDF') }}</a>
                </div>
            </x-card>
        @endif

        @if ($appointment->review)
            <x-card class="mt-6">
                <h2 class="text-lg font-semibold">{{ __('Your review') }}</h2>
                <p class="mt-2 text-amber-500">{{ str_repeat('★', $appointment->review->rating) }}{{ str_repeat('☆', 5 - $appointment->review->rating) }}</p>
                @if ($appointment->review->comment)
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $appointment->review->comment }}</p>
                @endif
            </x-card>
        @elseif ($appointment->isReviewable())
            <x-card class="mt-6">
                <h2 class="text-lg font-semibold">{{ __('Leave a review') }}</h2>
                <form method="POST" action="{{ route('appointments.review', $appointment) }}" class="mt-3 space-y-3" x-data="{ rating: {{ (int) old('rating', 5) }} }">
                    @csrf
                    <input type="hidden" name="rating" :value="rating">
                    <div class="flex gap-1 text-2xl">
                        <template x-for="star in 5" :key="star">
                            <button type="button" @click="rating = star"
                                    :class="star <= rating ? 'text-amber-500' : 'text-gray-300 dark:text-gray-600'">★</button>
                        </template>
                    </div>
                    <x-input-error for="rating" />
                    <textarea name="comment" rows="3" placeholder="{{ __('Tell us about your experience (optional)') }}"
                              class="w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2 text-sm">{{ old('comment') }}</textarea>
                    <x-input-error for="comment" />
                    <button type="submit" class="rounded-md bg-amber-500 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-amber-400 transition">{{ __('Submit review') }}</button>
                </form>
            </x-card>
        @endif

        @if ($appointment->isCancellable())
            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="mt-6"
                  onsubmit="return confirm(@js(__('Cancel this appointment?')));">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-sm text-red-600 hover:underline">{{ __('Cancel appointment') }}</button>
            </form>
        @endif
    </div>
</x-layout>
