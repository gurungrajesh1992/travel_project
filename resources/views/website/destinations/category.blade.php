<x-website-layout :title="$destination->name.' - '.$category->name">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <p class="text-sm text-gray-500">
            <a href="{{ route('destinations.show', $destination) }}" class="hover:text-primary">{{ $destination->name }}</a>
            @if ($category->parent)
                / <a href="{{ route('destinations.category', [$destination, $category->parent]) }}" class="hover:text-primary">{{ $category->parent->name }}</a>
            @endif
            / {{ $category->name }}
        </p>
        <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $destination->name }} {{ $category->name }}</h1>

        @include('website.destinations._category-pills', ['activeCategorySlug' => $category->slug])

        <div class="mt-8">
            @if ($tours->isEmpty())
                <x-ui.empty-state title="No tours yet" description="Check back soon for {{ $destination->name }} {{ $category->name }} tours." />
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
