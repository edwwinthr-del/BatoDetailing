<x-layout>
    <x-slot:title>{{ __('Job') }} #{{ $appointment->id }}</x-slot:title>

    <div class="mx-auto max-w-2xl">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ __('Job') }} #{{ $appointment->id }}</h1>
            <x-status-badge :status="$appointment->status" />
        </div>

        <x-card class="mt-6">
            <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Scheduled') }}</dt>
                    <dd class="mt-0.5 font-medium">{{ $appointment->scheduled_at->format('d.m.Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Customer') }}</dt>
                    <dd class="mt-0.5 font-medium">{{ $appointment->user->name }}<br><span class="text-gray-500 font-normal">{{ $appointment->user->phone }}</span></dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Vehicle') }}</dt>
                    <dd class="mt-0.5 font-medium">{{ $appointment->vehicle->displayName() }} ({{ $appointment->vehicle->license_plate }}, {{ __(ucfirst($appointment->vehicle->type)) }})</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Services') }}</dt>
                    <dd class="mt-0.5">{{ $appointment->services->pluck('name')->join(', ') }}</dd>
                </div>
                @if ($appointment->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Customer notes') }}</dt>
                        <dd class="mt-0.5">{{ $appointment->notes }}</dd>
                    </div>
                @endif
                @if ($appointment->vehicle->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Vehicle notes') }}</dt>
                        <dd class="mt-0.5">{{ $appointment->vehicle->notes }}</dd>
                    </div>
                @endif
            </dl>
        </x-card>

        @if (! in_array($appointment->status, [\App\Models\Appointment::STATUS_COMPLETED, \App\Models\Appointment::STATUS_CANCELLED, \App\Models\Appointment::STATUS_REJECTED]))
            <x-card class="mt-6">
                <h2 class="text-lg font-semibold">{{ __('Update job status') }}</h2>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Mark the job In progress when you start, Completed when you finish (this sends the invoice to the customer), or Cancelled if it cannot be done.') }}
                </p>
                <div class="mt-4 flex flex-wrap gap-3">
                    @if ($appointment->status !== \App\Models\Appointment::STATUS_IN_PROGRESS)
                        <form method="POST" action="{{ route('worker.appointments.status', $appointment) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 transition">
                                {{ __('Start job') }}
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('worker.appointments.status', $appointment) }}"
                          onsubmit="return confirm(@js(__('Finish this job? The invoice will be generated and emailed to the customer.')));">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-500 transition">
                            {{ __('Finish job') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('worker.appointments.status', $appointment) }}"
                          onsubmit="return confirm(@js(__('Cancel this job?')));">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="rounded-md border border-red-300 dark:border-red-800 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-950 transition">
                            {{ __('Cancel job') }}
                        </button>
                    </form>
                </div>
                <x-input-error for="status" class="mt-2" />
            </x-card>
        @endif

        <x-card class="mt-6">
            <h2 class="text-lg font-semibold">{{ __('Internal comments') }}</h2>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Visible to staff only — the customer cannot see these.') }}</p>

            <form method="POST" action="{{ route('worker.appointments.comments.store', $appointment) }}" class="mt-4 space-y-2">
                @csrf
                <textarea name="body" rows="2" required placeholder="{{ __('e.g. Heavy scratches on rear bumper, customer informed...') }}"
                          class="w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2 text-sm">{{ old('body') }}</textarea>
                <x-input-error for="body" />
                <button type="submit" class="rounded-md bg-gray-900 dark:bg-amber-500 px-4 py-2 text-sm font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                    {{ __('Add comment') }}
                </button>
            </form>

            <div class="mt-5 space-y-4">
                @forelse ($appointment->comments as $comment)
                    <div class="border-l-2 border-amber-400 pl-3">
                        <p class="text-sm">{{ $comment->body }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ $comment->user->name }} · {{ $comment->created_at->format('d.m.Y H:i') }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No comments yet.') }}</p>
                @endforelse
            </div>
        </x-card>
    </div>
</x-layout>
