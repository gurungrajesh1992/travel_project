<x-website-layout :title="$destination->name">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-bold text-gray-900">{{ $destination->name }} Tours</h1>
        @if ($destination->description)
            <p class="mt-2 text-gray-600 max-w-2xl">{{ $destination->description }}</p>
        @endif

        @include('website.destinations._category-pills', ['activeCategorySlug' => null])

        <div class="mt-8">
            @if ($tours->isEmpty())
                <x-ui.empty-state title="No tours yet" description="Check back soon for {{ $destination->name }} tours." />
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
