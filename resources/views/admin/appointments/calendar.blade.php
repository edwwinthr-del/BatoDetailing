<x-layout>
    <x-slot:title>{{ __('Calendar') }}</x-slot:title>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ __('Calendar') }} — {{ $month->format('F Y') }}</h1>
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.appointments.calendar', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}"
               class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-white">&larr; {{ __('Prev') }}</a>
            <a href="{{ route('admin.appointments.calendar') }}"
               class="rounded-md border border-gray-300 dark:border-gray-700 px-3 py-1.5">{{ __('Today') }}</a>
            <a href="{{ route('admin.appointments.calendar', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}"
               class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-white">{{ __('Next') }} &rarr;</a>
        </div>
    </div>

    @php
        $start = $month->copy()->startOfMonth()->startOfWeek();
        $end = $month->copy()->endOfMonth()->endOfWeek();
        $days = [];
        for ($day = $start->copy(); $day <= $end; $day->addDay()) {
            $days[] = $day->copy();
        }
    @endphp

    <x-card class="mt-6 overflow-x-auto p-0">
        <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
            @foreach ([__('Mon'), __('Tue'), __('Wed'), __('Thu'), __('Fri'), __('Sat'), __('Sun')] as $weekday)
                <div class="px-2 py-2 text-center">{{ $weekday }}</div>
            @endforeach
        </div>
        <div class="grid grid-cols-7">
            @foreach ($days as $day)
                <div class="min-h-24 border-b border-r border-gray-100 dark:border-gray-800 p-1.5 {{ $day->month !== $month->month ? 'bg-gray-50/60 dark:bg-gray-800/40 text-gray-400' : '' }}">
                    <p class="text-xs font-semibold {{ $day->isToday() ? 'text-amber-500' : '' }}">{{ $day->day }}</p>
                    <div class="mt-1 space-y-1">
                        @foreach ($appointments->get($day->format('Y-m-d'), collect()) as $appointment)
                            <a href="{{ route('admin.appointments.show', $appointment) }}"
                               class="block truncate rounded px-1.5 py-0.5 text-[11px] font-medium
                                      {{ in_array($appointment->status, ['cancelled', 'rejected']) ? 'bg-gray-200 dark:bg-gray-700 line-through text-gray-500' : 'bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 hover:bg-amber-200' }}">
                                {{ $appointment->scheduled_at->format('H:i') }} {{ $appointment->user->first_name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </x-card>
</x-layout>
