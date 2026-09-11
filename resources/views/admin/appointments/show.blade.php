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
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Customer') }}</dt>
                    <dd class="mt-0.5 font-medium">{{ $appointment->user->name }}<br><span class="text-gray-500 font-normal">{{ $appointment->user->email }} · {{ $appointment->user->phone }}</span></dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Scheduled') }}</dt>
                    <dd class="mt-0.5 font-medium">{{ $appointment->scheduled_at->format('d.m.Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Vehicle') }}</dt>
                    <dd class="mt-0.5 font-medium">{{ $appointment->vehicle->displayName() }} ({{ $appointment->vehicle->license_plate }}, {{ __(ucfirst($appointment->vehicle->type)) }})</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Total') }}</dt>
                    <dd class="mt-0.5 font-bold text-amber-600">{{ number_format((float) $appointment->total, 2) }}€</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Services') }}</dt>
                    <dd class="mt-1">{{ $appointment->services->pluck('name')->join(', ') }}</dd>
                </div>
                @if ($appointment->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Notes') }}</dt>
                        <dd class="mt-0.5">{{ $appointment->notes }}</dd>
                    </div>
                @endif
            </dl>
        </x-card>

        <x-card class="mt-6">
            <h2 class="text-lg font-semibold">{{ __('Change status') }}</h2>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Setting status to Completed automatically generates the invoice, awards loyalty points and emails the customer.') }}
            </p>
            <form method="POST" action="{{ route('admin.appointments.status', $appointment) }}" class="mt-3 flex items-center gap-3">
                @csrf
                @method('PATCH')
                <select name="status" class="rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2 text-sm">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected($appointment->status === $status)>{{ __(ucfirst(str_replace('_', ' ', $status))) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-md bg-gray-900 dark:bg-amber-500 px-4 py-2 text-sm font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                    {{ __('Update') }}
                </button>
            </form>
            <x-input-error for="status" class="mt-2" />
        </x-card>

        @if ($appointment->invoice)
            <x-card class="mt-6">
                <h2 class="text-lg font-semibold">{{ __('Invoice') }}</h2>
                <p class="mt-2 text-sm">{{ $appointment->invoice->number }} · <x-status-badge :status="$appointment->invoice->status" /></p>
            </x-card>
        @endif

        @if ($appointment->comments->isNotEmpty())
            <x-card class="mt-6">
                <h2 class="text-lg font-semibold">{{ __('Internal comments') }}</h2>
                <div class="mt-3 space-y-4">
                    @foreach ($appointment->comments as $comment)
                        <div class="border-l-2 border-amber-400 pl-3">
                            <p class="text-sm">{{ $comment->body }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $comment->user->name }} · {{ $comment->created_at->format('d.m.Y H:i') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif

        @if ($appointment->review)
            <x-card class="mt-6">
                <h2 class="text-lg font-semibold">{{ __('Customer review') }}</h2>
                <p class="mt-2 text-amber-500">{{ str_repeat('★', $appointment->review->rating) }}{{ str_repeat('☆', 5 - $appointment->review->rating) }}</p>
                @if ($appointment->review->comment)
                    <p class="mt-2 text-sm">{{ $appointment->review->comment }}</p>
                @endif
            </x-card>
        @endif
    </div>
</x-layout>
