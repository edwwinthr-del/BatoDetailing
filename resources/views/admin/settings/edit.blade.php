<x-layout>
    <x-slot:title>{{ __('System settings') }}</x-slot:title>

    <x-card class="mx-auto max-w-lg p-8">
        <h1 class="text-2xl font-bold">{{ __('System settings') }}</h1>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <h2 class="font-semibold">{{ __('Company info') }}</h2>
                <div>
                    <label for="company_name" class="block text-sm font-medium">{{ __('Company name') }}</label>
                    <input id="company_name" name="company_name" type="text" value="{{ old('company_name', $settings['company.name']) }}" required
                           class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                    <x-input-error for="company_name" />
                </div>
                <div>
                    <label for="company_email" class="block text-sm font-medium">{{ __('Email') }}</label>
                    <input id="company_email" name="company_email" type="email" value="{{ old('company_email', $settings['company.email']) }}" required
                           class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                    <x-input-error for="company_email" />
                </div>
                <div>
                    <label for="company_phone" class="block text-sm font-medium">{{ __('Phone') }}</label>
                    <input id="company_phone" name="company_phone" type="text" value="{{ old('company_phone', $settings['company.phone']) }}" required
                           class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                    <x-input-error for="company_phone" />
                </div>
                <div>
                    <label for="company_address" class="block text-sm font-medium">{{ __('Address') }}</label>
                    <input id="company_address" name="company_address" type="text" value="{{ old('company_address', $settings['company.address']) }}" required
                           class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                    <x-input-error for="company_address" />
                </div>
            </div>

            <div>
                <h2 class="font-semibold">{{ __('Theme') }}</h2>
                <label for="default_theme" class="mt-2 block text-sm font-medium">{{ __('Default theme for new visitors') }}</label>
                <select id="default_theme" name="default_theme" class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                    <option value="light" @selected(old('default_theme', $settings['theme.default']) === 'light')>{{ __('Light') }}</option>
                    <option value="dark" @selected(old('default_theme', $settings['theme.default']) === 'dark')>{{ __('Dark') }}</option>
                </select>
                <x-input-error for="default_theme" />
            </div>

            <div class="space-y-4">
                <h2 class="font-semibold">{{ __('Loyalty rules') }}</h2>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label for="points_per_euro" class="block text-sm font-medium">{{ __('Points per €') }}</label>
                        <input id="points_per_euro" name="points_per_euro" type="number" step="0.1" min="0" value="{{ old('points_per_euro', $settings['loyalty.points_per_euro']) }}" required
                               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                        <x-input-error for="points_per_euro" />
                    </div>
                    <div>
                        <label for="redeem_value" class="block text-sm font-medium">{{ __('€ per point') }}</label>
                        <input id="redeem_value" name="redeem_value" type="number" step="0.01" min="0" value="{{ old('redeem_value', $settings['loyalty.redeem_value']) }}" required
                               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                        <x-input-error for="redeem_value" />
                    </div>
                    <div>
                        <label for="min_redeem" class="block text-sm font-medium">{{ __('Min. redeem') }}</label>
                        <input id="min_redeem" name="min_redeem" type="number" min="0" value="{{ old('min_redeem', $settings['loyalty.min_redeem']) }}" required
                               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                        <x-input-error for="min_redeem" />
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full rounded-md bg-gray-900 dark:bg-amber-500 px-4 py-2 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                {{ __('Save settings') }}
            </button>
        </form>
    </x-card>
</x-layout>
