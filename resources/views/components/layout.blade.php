@props(['fullWidth' => false])
@inject('settings', 'App\Services\SettingsService')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' — ' : '' }}{{ $settings->get('company.name') }}</title>
    <script>
        (function () {
            const stored = localStorage.getItem('theme');
            const theme = stored || @json($settings->get('theme.default'));
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-gray-100 text-gray-900 dark:bg-gray-950 dark:text-gray-100 font-sans antialiased transition-colors">
    <nav class="bg-gray-900 text-white sticky top-0 z-40 shadow-lg" x-data="{ open: false }">
        <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="text-lg font-bold tracking-tight">Bato<span class="text-amber-400">Detailing</span></a>
                <div class="hidden md:flex items-center gap-5">
                    <a href="{{ route('home') }}#services" class="text-sm text-gray-300 hover:text-white transition">{{ __('Services') }}</a>
                    <a href="{{ route('home') }}#gallery" class="text-sm text-gray-300 hover:text-white transition">{{ __('Gallery') }}</a>
                    <a href="{{ route('home') }}#contact" class="text-sm text-gray-300 hover:text-white transition">{{ __('Contact') }}</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-300 hover:text-white transition">{{ __('Dashboard') }}</a>
                        <a href="{{ route('vehicles.index') }}" class="text-sm text-gray-300 hover:text-white transition">{{ __('My Vehicles') }}</a>
                        <a href="{{ route('appointments.index') }}" class="text-sm text-gray-300 hover:text-white transition">{{ __('Appointments') }}</a>
                        <a href="{{ route('invoices.index') }}" class="text-sm text-gray-300 hover:text-white transition">{{ __('Invoices') }}</a>
                        @can('worker')
                            <a href="{{ route('worker.appointments.index') }}" class="text-sm text-amber-300 hover:text-amber-200 transition">{{ __('Work queue') }}</a>
                        @endcan
                        @can('admin')
                            <a href="{{ route('admin.dashboard') }}" class="text-sm text-amber-300 hover:text-amber-200 transition">{{ __('Admin') }}</a>
                        @endcan
                    @endauth
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Language switcher --}}
                <div class="flex items-center gap-1 text-xs font-semibold">
                    <a href="{{ route('locale.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'text-amber-400' : 'text-gray-400 hover:text-white' }}">EN</a>
                    <span class="text-gray-600">|</span>
                    <a href="{{ route('locale.switch', 'sr') }}" class="{{ app()->getLocale() === 'sr' ? 'text-amber-400' : 'text-gray-400 hover:text-white' }}">SR</a>
                </div>

                {{-- Theme toggle --}}
                <button type="button"
                        class="rounded-md p-1.5 text-gray-300 hover:text-white hover:bg-gray-800 transition"
                        title="{{ __('Toggle theme') }}"
                        onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </button>

                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ route('profile.edit') }}" class="text-sm text-gray-300 hover:text-white transition">{{ auth()->user()->first_name }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-300 hover:text-white transition">{{ __('Log out') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-300 hover:text-white transition">{{ __('Log in') }}</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-amber-500 px-3 py-1.5 text-sm font-semibold text-gray-900 hover:bg-amber-400 transition">{{ __('Register') }}</a>
                    @endauth
                </div>

                {{-- Mobile menu button --}}
                <button type="button" class="md:hidden rounded-md p-1.5 text-gray-300 hover:text-white" @click="open = !open">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div class="md:hidden border-t border-gray-800 px-4 py-3 space-y-2" x-show="open" x-cloak x-transition>
            <a href="{{ route('home') }}#services" class="block text-sm text-gray-300 hover:text-white">{{ __('Services') }}</a>
            <a href="{{ route('home') }}#contact" class="block text-sm text-gray-300 hover:text-white">{{ __('Contact') }}</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block text-sm text-gray-300 hover:text-white">{{ __('Dashboard') }}</a>
                <a href="{{ route('vehicles.index') }}" class="block text-sm text-gray-300 hover:text-white">{{ __('My Vehicles') }}</a>
                <a href="{{ route('appointments.index') }}" class="block text-sm text-gray-300 hover:text-white">{{ __('Appointments') }}</a>
                <a href="{{ route('invoices.index') }}" class="block text-sm text-gray-300 hover:text-white">{{ __('Invoices') }}</a>
                <a href="{{ route('profile.edit') }}" class="block text-sm text-gray-300 hover:text-white">{{ __('Profile') }}</a>
                @can('worker')
                    <a href="{{ route('worker.appointments.index') }}" class="block text-sm text-amber-300">{{ __('Work queue') }}</a>
                @endcan
                @can('admin')
                    <a href="{{ route('admin.dashboard') }}" class="block text-sm text-amber-300">{{ __('Admin') }}</a>
                @endcan
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-300 hover:text-white">{{ __('Log out') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block text-sm text-gray-300 hover:text-white">{{ __('Log in') }}</a>
                <a href="{{ route('register') }}" class="block text-sm text-amber-300">{{ __('Register') }}</a>
            @endauth
        </div>
    </nav>

    <main class="flex-1 w-full {{ ($fullWidth ?? false) ? '' : 'mx-auto max-w-7xl px-4 py-8' }}">
        @if (session('status'))
            <div class="{{ ($fullWidth ?? false) ? 'mx-auto max-w-7xl px-4 pt-4' : '' }}">
                <div class="mb-6 rounded-md bg-green-100 border border-green-300 px-4 py-3 text-sm text-green-800 dark:bg-green-900/40 dark:border-green-700 dark:text-green-200">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        {{ $slot }}
    </main>

    <footer class="bg-gray-900 text-gray-300 mt-auto">
        <div class="mx-auto max-w-7xl px-4 py-10 grid gap-8 md:grid-cols-3">
            <div>
                <p class="text-lg font-bold text-white">Bato<span class="text-amber-400">Detailing</span></p>
                <p class="mt-2 text-sm">{{ $settings->get('company.address') }}</p>
                <p class="mt-1 text-sm">{{ __('Phone') }}: {{ $settings->get('company.phone') }}</p>
                <p class="mt-1 text-sm">{{ __('Email') }}: <a href="mailto:{{ $settings->get('company.email') }}" class="text-amber-400 hover:underline">{{ $settings->get('company.email') }}</a></p>
            </div>
            <div>
                <p class="text-sm font-semibold text-white uppercase tracking-wider">{{ __('Support') }}</p>
                <form method="POST" action="{{ route('contact.send') }}" class="mt-3 space-y-2">
                    @csrf
                    <input type="text" name="name" placeholder="{{ __('Your name') }}" required
                           class="w-full rounded-md bg-gray-800 border border-gray-700 px-3 py-1.5 text-sm placeholder-gray-500">
                    <input type="email" name="email" placeholder="{{ __('Your email') }}" required
                           class="w-full rounded-md bg-gray-800 border border-gray-700 px-3 py-1.5 text-sm placeholder-gray-500">
                    <textarea name="message" rows="2" placeholder="{{ __('How can we help?') }}" required
                              class="w-full rounded-md bg-gray-800 border border-gray-700 px-3 py-1.5 text-sm placeholder-gray-500"></textarea>
                    <button type="submit" class="rounded-md bg-amber-500 px-3 py-1.5 text-sm font-semibold text-gray-900 hover:bg-amber-400 transition">{{ __('Send') }}</button>
                </form>
            </div>
            <div>
                <p class="text-sm font-semibold text-white uppercase tracking-wider">{{ __('Links') }}</p>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('privacy') }}" class="hover:text-white transition">{{ __('Privacy Policy') }}</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-white transition">{{ __('Terms of Service') }}</a></li>
                    <li><a href="{{ route('home') }}#services" class="hover:text-white transition">{{ __('Services') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 py-4 text-center text-xs text-gray-500">
            © {{ now()->year }} {{ $settings->get('company.name') }}. {{ __('All rights reserved.') }}
        </div>
    </footer>
</body>
</html>
