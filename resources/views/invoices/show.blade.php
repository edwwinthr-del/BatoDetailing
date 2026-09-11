<x-layout>
    <x-slot:title>{{ $invoice->number }}</x-slot:title>

    <div class="mx-auto max-w-2xl">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ $invoice->number }}</h1>
            <div class="flex items-center gap-3">
                <x-status-badge :status="$invoice->status" />
                <a href="{{ route('invoices.download', $invoice) }}"
                   class="rounded-md bg-amber-500 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-amber-400 transition">{{ __('Download PDF') }}</a>
            </div>
        </div>

        <x-card class="mt-6">
            <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Issued') }}</dt>
                    <dd class="mt-0.5 font-medium">{{ $invoice->issued_at->format('d.m.Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Appointment') }}</dt>
                    <dd class="mt-0.5 font-medium">
                        <a href="{{ route('appointments.show', $invoice->appointment) }}" class="text-amber-600 hover:underline">
                            {{ $invoice->appointment->scheduled_at->format('d.m.Y H:i') }}
                        </a>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Vehicle') }}</dt>
                    <dd class="mt-0.5 font-medium">{{ $invoice->appointment->vehicle->displayName() }}</dd>
                </div>
            </dl>

            <div class="mt-4 border-t border-gray-100 dark:border-gray-800 pt-4 text-sm space-y-1">
                @foreach ($invoice->appointment->services as $service)
                    <p class="flex justify-between"><span>{{ $service->name }}</span> <span>{{ number_format((float) $service->pivot->price, 2) }}€</span></p>
                @endforeach
                <p class="flex justify-between"><span>{{ __('Vehicle type surcharge') }}</span> <span>{{ number_format((float) $invoice->appointment->type_modifier, 2) }}€</span></p>
                @if ((float) $invoice->discount > 0)
                    <p class="flex justify-between"><span>{{ __('Loyalty discount') }}</span> <span>-{{ number_format((float) $invoice->discount, 2) }}€</span></p>
                @endif
                <p class="flex justify-between border-t border-gray-100 dark:border-gray-800 pt-2 font-bold text-base">
                    <span>{{ __('Total') }}</span> <span class="text-amber-600">{{ number_format((float) $invoice->total, 2) }}€</span>
                </p>
            </div>
        </x-card>
    </div>
</x-layout>
