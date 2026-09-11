<x-layout :full-width="true">
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gray-950 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(245,158,11,0.18),transparent_60%),radial-gradient(ellipse_at_bottom_left,rgba(59,130,246,0.12),transparent_60%)]"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-28 lg:py-40">
            <p class="reveal text-sm font-semibold uppercase tracking-[0.3em] text-amber-400">{{ __('Premium car care') }}</p>
            <h1 class="reveal reveal-delay-1 mt-4 max-w-3xl text-4xl font-bold tracking-tight sm:text-6xl">
                {{ __('Your car deserves') }} <span class="text-amber-400">{{ __('perfection') }}</span>
            </h1>
            <p class="reveal reveal-delay-2 mt-6 max-w-2xl text-lg text-gray-300">
                {{ __('From a quick refresh to full premium detailing and ceramic-grade finishes — BatoDetailing brings showroom shine back to your vehicle.') }}
            </p>
            <div class="reveal reveal-delay-3 mt-10 flex flex-wrap gap-4">
                <a href="{{ auth()->check() ? route('appointments.create') : route('register') }}"
                   class="rounded-md bg-amber-500 px-6 py-3 font-semibold text-gray-900 hover:bg-amber-400 transition shadow-lg shadow-amber-500/25">
                    {{ __('Book Appointment') }}
                </a>
                <a href="#contact" class="rounded-md border border-gray-600 px-6 py-3 font-semibold text-white hover:bg-gray-800 transition">
                    {{ __('Contact Us') }}
                </a>
            </div>

            {{-- Counters --}}
            <div class="mt-20 grid grid-cols-3 gap-6 max-w-xl">
                <div class="reveal">
                    <p class="text-3xl font-bold text-amber-400" data-counter="{{ $stats['completed'] }}">0</p>
                    <p class="mt-1 text-sm text-gray-400">{{ __('Cars detailed') }}</p>
                </div>
                <div class="reveal reveal-delay-1">
                    <p class="text-3xl font-bold text-amber-400" data-counter="{{ $stats['customers'] }}">0</p>
                    <p class="mt-1 text-sm text-gray-400">{{ __('Happy customers') }}</p>
                </div>
                <div class="reveal reveal-delay-2">
                    <p class="text-3xl font-bold text-amber-400" data-counter="{{ $stats['services'] }}">0</p>
                    <p class="mt-1 text-sm text-gray-400">{{ __('Services offered') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- About --}}
    <section id="about" class="mx-auto max-w-7xl px-4 py-20">
        <div class="grid gap-10 lg:grid-cols-2 items-center">
            <div class="reveal">
                <p class="text-sm font-semibold uppercase tracking-widest text-amber-500">{{ __('About us') }}</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight">{{ __('Craftsmanship in every detail') }}</h2>
                <p class="mt-4 text-gray-600 dark:text-gray-400">
                    {{ __('BatoDetailing is a specialist car detailing studio. We treat every vehicle — from daily drivers to exotics — with the same obsessive attention to detail, using premium products and proven techniques.') }}
                </p>
                <ul class="mt-6 space-y-3 text-sm text-gray-700 dark:text-gray-300">
                    <li class="flex items-center gap-2"><span class="text-amber-500">✔</span> {{ __('Certified detailing professionals') }}</li>
                    <li class="flex items-center gap-2"><span class="text-amber-500">✔</span> {{ __('Premium, paint-safe products only') }}</li>
                    <li class="flex items-center gap-2"><span class="text-amber-500">✔</span> {{ __('Loyalty points on every visit') }}</li>
                </ul>
            </div>
            <div class="reveal reveal-delay-1 grid grid-cols-2 gap-4">
                <div class="aspect-square rounded-2xl bg-gradient-to-br from-gray-800 to-gray-950 flex items-center justify-center text-6xl">🚗</div>
                <div class="aspect-square rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-6xl mt-8">✨</div>
            </div>
        </div>
    </section>

    {{-- Services --}}
    <section id="services" class="bg-white dark:bg-gray-900 py-20">
        <div class="mx-auto max-w-7xl px-4">
            <div class="reveal text-center">
                <p class="text-sm font-semibold uppercase tracking-widest text-amber-500">{{ __('Services') }}</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight">{{ __('Choose your treatment') }}</h2>
            </div>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)
                    <div class="reveal {{ 'reveal-delay-'.($loop->index % 3) }} group rounded-xl border border-gray-200 dark:border-gray-800 p-6 hover:border-amber-400 hover:shadow-xl transition">
                        <div class="flex items-baseline justify-between">
                            <h3 class="font-semibold text-lg">{{ $service->name }}</h3>
                            <span class="text-xl font-bold text-amber-500">{{ number_format((float) $service->base_price, 0) }}€<span class="text-xs text-gray-400 font-normal">+</span></span>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $service->description }}</p>
                        <p class="mt-4 text-xs text-gray-400">~{{ $service->duration_minutes }} {{ __('min') }} · {{ __('price varies by vehicle type') }}</p>
                    </div>
                @endforeach
            </div>
            <div class="reveal mt-10 text-center">
                <a href="{{ auth()->check() ? route('appointments.create') : route('register') }}"
                   class="inline-block rounded-md bg-gray-900 dark:bg-amber-500 px-6 py-3 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                    {{ __('Book Appointment') }}
                </a>
            </div>
        </div>
    </section>

    {{-- Before/After gallery --}}
    <section id="gallery" class="mx-auto max-w-7xl px-4 py-20">
        <div class="reveal text-center">
            <p class="text-sm font-semibold uppercase tracking-widest text-amber-500">{{ __('Gallery') }}</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight">{{ __('Before & After') }}</h2>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([['🌧️', '☀️', __('Exterior transformation')], ['🧹', '🛋️', __('Interior deep clean')], ['🛞', '💎', __('Wheel & trim restoration')]] as [$before, $after, $label])
                <div class="reveal {{ 'reveal-delay-'.($loop->index % 3) }} overflow-hidden rounded-xl bg-white dark:bg-gray-900 shadow">
                    <div class="grid grid-cols-2">
                        <div class="aspect-square bg-gradient-to-br from-gray-400 to-gray-600 flex flex-col items-center justify-center text-5xl">
                            {{ $before }}
                            <span class="mt-2 text-xs font-semibold uppercase tracking-wider text-white/80">{{ __('Before') }}</span>
                        </div>
                        <div class="aspect-square bg-gradient-to-br from-amber-300 to-amber-500 flex flex-col items-center justify-center text-5xl">
                            {{ $after }}
                            <span class="mt-2 text-xs font-semibold uppercase tracking-wider text-gray-900/70">{{ __('After') }}</span>
                        </div>
                    </div>
                    <p class="px-4 py-3 text-sm font-medium">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Testimonials --}}
    <section id="testimonials" class="bg-white dark:bg-gray-900 py-20">
        <div class="mx-auto max-w-7xl px-4">
            <div class="reveal text-center">
                <p class="text-sm font-semibold uppercase tracking-widest text-amber-500">{{ __('Testimonials') }}</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight">{{ __('What our customers say') }}</h2>
            </div>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($testimonials as $review)
                    <div class="reveal {{ 'reveal-delay-'.($loop->index % 3) }} rounded-xl border border-gray-200 dark:border-gray-800 p-6">
                        <p class="text-amber-500">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</p>
                        <p class="mt-3 text-sm text-gray-700 dark:text-gray-300">“{{ $review->comment }}”</p>
                        <p class="mt-4 text-sm font-semibold">{{ $review->user->first_name }} {{ mb_substr($review->user->last_name, 0, 1) }}.</p>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-500">{{ __('Be the first to leave a review after your appointment!') }}</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Contact --}}
    <section id="contact" class="mx-auto max-w-7xl px-4 py-20">
        <div class="grid gap-10 lg:grid-cols-2">
            <div class="reveal">
                <p class="text-sm font-semibold uppercase tracking-widest text-amber-500">{{ __('Contact') }}</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight">{{ __('Get in touch') }}</h2>
                <p class="mt-4 text-gray-600 dark:text-gray-400">{{ __('Questions about our services or your booking? Send us a message and we will get back to you quickly.') }}</p>
                <div class="mt-6 space-y-2 text-sm">
                    <p><span class="font-semibold">{{ __('Phone') }}:</span> {{ $company['phone'] }}</p>
                    <p><span class="font-semibold">{{ __('Email') }}:</span> {{ $company['email'] }}</p>
                    <p><span class="font-semibold">{{ __('Address') }}:</span> {{ $company['address'] }}</p>
                </div>
            </div>
            <x-card class="reveal reveal-delay-1">
                <form method="POST" action="{{ route('contact.send') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="contact-name" class="block text-sm font-medium">{{ __('Name') }}</label>
                        <input id="contact-name" name="name" type="text" value="{{ old('name') }}" required
                               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                        <x-input-error for="name" />
                    </div>
                    <div>
                        <label for="contact-email" class="block text-sm font-medium">{{ __('Email') }}</label>
                        <input id="contact-email" name="email" type="email" value="{{ old('email') }}" required
                               class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">
                        <x-input-error for="email" />
                    </div>
                    <div>
                        <label for="contact-message" class="block text-sm font-medium">{{ __('Message') }}</label>
                        <textarea id="contact-message" name="message" rows="4" required
                                  class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2">{{ old('message') }}</textarea>
                        <x-input-error for="message" />
                    </div>
                    <button type="submit" class="rounded-md bg-gray-900 dark:bg-amber-500 px-5 py-2.5 font-semibold text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-amber-400 transition">
                        {{ __('Send message') }}
                    </button>
                </form>
            </x-card>
        </div>
    </section>
</x-layout>
