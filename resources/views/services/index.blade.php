<x-layout>
    <x-slot:title>Services</x-slot:title>

    <h1 class="text-2xl font-bold">Our Services</h1>

    <div class="mt-6 space-y-8">
        @forelse ($services as $service)
            <section class="rounded-lg bg-white p-6 shadow">
                <h2 class="text-xl font-semibold">{{ $service->name }}</h2>
                @if ($service->description)
                    <p class="mt-1 text-gray-600">{{ $service->description }}</p>
                @endif

                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($service->packages as $package)
                        <div class="rounded-md border border-gray-200 p-4">
                            <div class="flex items-baseline justify-between">
                                <h3 class="font-medium">{{ $package->name }}</h3>
                                <span class="font-bold">₱{{ number_format($package->price, 2) }}</span>
                            </div>
                            @if ($package->description)
                                <p class="mt-1 text-sm text-gray-600">{{ $package->description }}</p>
                            @endif
                            <p class="mt-2 text-xs text-gray-500">~{{ $package->duration_minutes }} min</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            <p class="text-gray-600">No services available yet.</p>
        @endforelse
    </div>

    <div class="mt-8">
        @auth
            <a href="{{ route('bookings.create') }}" class="rounded-md bg-sky-500 px-5 py-2.5 text-white font-medium hover:bg-sky-400">Book a service</a>
        @else
            <a href="{{ route('login') }}" class="rounded-md bg-sky-500 px-5 py-2.5 text-white font-medium hover:bg-sky-400">Log in to book</a>
        @endauth
    </div>
</x-layout>
