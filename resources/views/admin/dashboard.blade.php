<x-layout>
    <x-slot:title>{{ __('Admin Dashboard') }}</x-slot:title>

    <h1 class="text-2xl font-bold">{{ __('Admin Dashboard') }}</h1>

    <div class="mt-4 flex flex-wrap gap-2 text-sm">
        <a href="{{ route('admin.users.index') }}" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-white hover:bg-gray-700">{{ __('Users') }}</a>
        <a href="{{ route('admin.vehicles.index') }}" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-white hover:bg-gray-700">{{ __('Vehicles') }}</a>
        <a href="{{ route('admin.appointments.index') }}" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-white hover:bg-gray-700">{{ __('Appointments') }}</a>
        <a href="{{ route('admin.appointments.calendar') }}" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-white hover:bg-gray-700">{{ __('Calendar') }}</a>
        <a href="{{ route('admin.services.index') }}" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-white hover:bg-gray-700">{{ __('Services') }}</a>
        <a href="{{ route('admin.pricing.edit') }}" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-white hover:bg-gray-700">{{ __('Pricing') }}</a>
        <a href="{{ route('admin.invoices.index') }}" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-white hover:bg-gray-700">{{ __('Invoices') }}</a>
        <a href="{{ route('admin.reports.index') }}" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-white hover:bg-gray-700">{{ __('Reports') }}</a>
        <a href="{{ route('admin.campaigns.create') }}" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-white hover:bg-gray-700">{{ __('Campaigns') }}</a>
        <a href="{{ route('admin.settings.edit') }}" class="rounded-md bg-gray-900 dark:bg-gray-800 px-3 py-1.5 text-white hover:bg-gray-700">{{ __('Settings') }}</a>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total users') }}</p>
            <p class="mt-1 text-3xl font-bold">{{ $totalUsers }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total appointments') }}</p>
            <p class="mt-1 text-3xl font-bold">{{ $totalAppointments }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Pending approvals') }}</p>
            <p class="mt-1 text-3xl font-bold text-yellow-500">{{ $pendingAppointments }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Revenue') }}</p>
            <p class="mt-1 text-3xl font-bold text-amber-500">{{ number_format($totalRevenue, 2) }}€</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Satisfaction') }}</p>
            <p class="mt-1 text-3xl font-bold">{{ $satisfaction ?: '—' }}<span class="text-base text-gray-400">/5</span></p>
            <p class="text-xs text-gray-400">{{ $reviewCount }} {{ __('reviews') }}</p>
        </x-card>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <x-card>
            <h2 class="font-semibold">{{ __('Monthly revenue') }} (€)</h2>
            <canvas id="revenueChart" height="220"></canvas>
        </x-card>
        <x-card>
            <h2 class="font-semibold">{{ __('Monthly appointments') }}</h2>
            <canvas id="appointmentsChart" height="220"></canvas>
        </x-card>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const labels = @json($chartLabels);

            new Chart(document.getElementById('revenueChart'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: @json(__('Revenue')),
                        data: @json($chartRevenue),
                        backgroundColor: 'rgba(245, 158, 11, 0.7)',
                        borderRadius: 4,
                    }],
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } },
            });

            new Chart(document.getElementById('appointmentsChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: @json(__('Appointments')),
                        data: @json($chartAppointments),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.15)',
                        fill: true,
                        tension: 0.3,
                    }],
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
            });
        });
    </script>
</x-layout>
