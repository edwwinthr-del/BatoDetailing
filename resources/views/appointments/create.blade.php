<x-layout>
    <x-slot:title>{{ __('Book Appointment') }}</x-slot:title>

    <x-card class="mx-auto max-w-2xl p-8">
        <h1 class="text-2xl font-bold">{{ __('Book a service') }}</h1>

        @if ($vehicles->isEmpty())
            <p class="mt-4 text-gray-600 dark:text-gray-400">{{ __('You need to add a vehicle before booking.') }}</p>
            <a href="{{ route('vehicles.create') }}" class="mt-4 inline-block rounded-md bg-amber-500 px-4 py-2 font-semibold text-gray-900 hover:bg-amber-400 transition">{{ __('Add a vehicle') }}</a>
        @else
            <div x-data="booking()" class="mt-6">
                <form method="POST" action="{{ route('appointments.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="vehicle_id" class="block text-sm font-medium">{{ __('Vehicle') }}</label>
                        <select id="vehicle_id" name="vehicle_id" required x-model="vehicleId"
                                class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>
                                    {{ $vehicle->displayName() }} ({{ $vehicle->license_plate }}) — {{ __(ucfirst($vehicle->type)) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error for="vehicle_id" />
                    </div>

                    <div>
                        <span class="block text-sm font-medium">{{ __('Services') }}</span>
                        <div class="mt-2 space-y-2">
                            @foreach ($services as $service)
                                <label class="flex items-start gap-3 rounded-md border border-gray-200 dark:border-gray-700 p-3 hover:border-amber-400 transition cursor-pointer">
                                    <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" x-model="selectedServices"
                                           @checked(in_array($service->id, old('service_ids', [])))
                                           class="mt-1 rounded border-gray-300">
                                    <span class="flex-1">
                                        <span class="flex items-baseline justify-between">
                                            <span class="font-medium">{{ $service->name }}</span>
                                            <span class="font-semibold text-amber-600">{{ number_format((float) $service->base_price, 2) }}€</span>
                                        </span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $service->description }} (~{{ $service->duration_minutes }} {{ __('min') }})</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error for="service_ids" />
                    </div>

                    <div>
                        <label for="scheduled_at" class="block text-sm font-medium">{{ __('Date & time') }}</label>
                        <input id="scheduled_at" name="scheduled_at" type="datetime-local" value="{{ old('scheduled_at') }}" required step="3600"
                               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Working hours') }}: {{ str_pad($businessHours['open'], 2, '0', STR_PAD_LEFT) }}:00–{{ str_pad($businessHours['close'], 2, '0', STR_PAD_LEFT) }}:00.
                            {{ __('Bookings are made in 1-hour slots.') }}
                        </p>
                        <x-input-error for="scheduled_at" />
                    </div>

                    @if ($loyaltyBalance >= $minRedeem)
                        <div>
                            <label for="redeem_points" class="block text-sm font-medium">
                                {{ __('Redeem loyalty points') }} ({{ __('you have :points', ['points' => $loyaltyBalance]) }})
                            </label>
                            <input id="redeem_points" name="redeem_points" type="number" min="0" max="{{ $loyaltyBalance }}"
                                   value="{{ old('redeem_points', 0) }}" x-model.number="redeemPoints"
                                   class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('1 point = :value € discount. Minimum :min points.', ['value' => $redeemValue, 'min' => $minRedeem]) }}
                            </p>
                            <x-input-error for="redeem_points" />
                        </div>
                    @endif

                    <div>
                        <label for="notes" class="block text-sm font-medium">{{ __('Notes (optional)') }}</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">{{ old('notes') }}</textarea>
                        <x-input-error for="notes" />
                    </div>

                    {{-- Live price estimate --}}
                    <div class="rounded-md bg-gray-50 dark:bg-gray-800 p-4 text-sm space-y-1">
                        <p class="flex justify-between"><span>{{ __('Services subtotal') }}</span> <span x-text="subtotal.toFixed(2) + ' €'"></span></p>
                        <p class="flex justify-between"><span>{{ __('Vehicle type surcharge') }}</span> <span x-text="modifier.toFixed(2) + ' €'"></span></p>
                        <p class="flex justify-between" x-show="discount > 0"><span>{{ __('Loyalty discount') }}</span> <span x-text="'-' + discount.toFixed(2) + ' €'"></span></p>
                        <p class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2 font-bold text-base">
                            <span>{{ __('Total') }}</span> <span class="text-amber-600" x-text="total.toFixed(2) + ' €'"></span>
                        </p>
                    </div>

                    <button type="submit" class="w-full rounded-md bg-gray-900 dark:bg-amber-500 px-4 py-2.5 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                        {{ __('Book Appointment') }}
                    </button>
                </form>
            </div>

            <script>
                function booking() {
                    const servicePrices = @json($services->pluck('base_price', 'id')->map(fn ($p) => (float) $p));
                    const vehicleTypes = @json($vehicles->pluck('type', 'id'));
                    const modifiers = @json($modifiers);
                    const redeemValue = {{ (float) $redeemValue }};

                    return {
                        vehicleId: @json(old('vehicle_id', $vehicles->first()->id)),
                        selectedServices: @json(array_map('strval', old('service_ids', []))),
                        redeemPoints: {{ (int) old('redeem_points', 0) }},
                        get subtotal() {
                            return this.selectedServices.reduce((sum, id) => sum + (servicePrices[id] || 0), 0);
                        },
                        get modifier() {
                            return this.selectedServices.length ? (modifiers[vehicleTypes[this.vehicleId]] || 0) : 0;
                        },
                        get discount() {
                            return Math.min(this.redeemPoints * redeemValue, this.subtotal + this.modifier);
                        },
                        get total() {
                            return Math.max(this.subtotal + this.modifier - this.discount, 0);
                        },
                    };
                }
            </script>
        @endif
    </x-card>
</x-layout>
