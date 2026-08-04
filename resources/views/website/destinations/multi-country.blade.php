<x-website-layout title="Multi-Country Tours">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-bold text-gray-900">Multi-Country Tours</h1>
        <p class="mt-2 text-gray-600">Tours that combine two or more countries in a single itinerary.</p>

        @if ($combos->isNotEmpty())
            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ route('destinations.multi-country') }}"
                   class="px-3 py-1.5 rounded-full text-sm {{ empty($selectedSlugs) ? 'bg-primary text-primary-content' : 'bg-gray-100 text-gray-700 hover:bg-primary hover:text-primary-content' }}">
                    All Combinations
                </a>
                @foreach ($combos as $combo)
                    <a href="{{ route('destinations.multi-country', ['destinations' => $combo['slugs']]) }}"
                       class="px-3 py-1.5 rounded-full text-sm {{ request('destinations') === $combo['slugs'] ? 'bg-primary text-primary-content' : 'bg-gray-100 text-gray-700 hover:bg-primary hover:text-primary-content' }}">
                        {{ $combo['label'] }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-8">
            @if ($tours->isEmpty())
                <x-ui.empty-state title="No multi-country tours yet" description="Check back soon." />
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($tours as $tour)
                        <x-website.tour-card :tour="$tour" />
                    @endforeach
                </div>
                <div class="mt-8">{{ $tours->links() }}</div>
            @endif
        </div>
    </div>
</x-website-layout>
