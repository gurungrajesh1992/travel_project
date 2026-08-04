<x-website-layout title="Home">
    <section class="bg-secondary text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <h1 class="text-3xl sm:text-5xl font-bold">Trek. Explore. Discover the Himalayas.</h1>
            <p class="mt-4 text-lg text-white/80 max-w-2xl mx-auto">
                Trekking, expeditions, and cultural tours across Nepal, India, Bhutan, and Tibet.
            </p>
            <div class="mt-8">
                <x-ui.button as="a" href="{{ route('tours.index') }}" size="lg">Browse Tours</x-ui.button>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Explore by Destination</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach ($destinations as $destination)
                <a href="{{ route('destinations.show', $destination) }}" class="group relative overflow-hidden rounded-lg border border-gray-200 text-center hover:border-primary transition">
                    @if ($destination->thumbnail)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($destination->thumbnail) }}" alt="{{ $destination->name }}"
                             class="absolute inset-0 h-full w-full object-cover">
                        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition"></div>
                        <p class="relative z-10 flex items-center justify-center h-32 p-6 font-semibold text-white">{{ $destination->name }}</p>
                    @else
                        <p class="flex items-center justify-center h-32 p-6 font-semibold group-hover:text-primary transition">{{ $destination->name }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    </section>

    @if ($featuredTours->isNotEmpty())
        <section class="bg-gray-50 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Featured Tours</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($featuredTours as $tour)
                        <x-website.tour-card :tour="$tour" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($latestPosts->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">From the Blog</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach ($latestPosts as $post)
                    <a href="{{ route('blog.show', $post) }}" class="block bg-white rounded-lg border border-gray-200 p-5 hover:shadow-md transition">
                        <p class="font-semibold text-gray-900">{{ $post->title }}</p>
                        <p class="text-sm text-gray-500 mt-2">{{ $post->excerpt }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-website-layout>
