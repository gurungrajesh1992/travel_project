<x-website-layout title="Home">
    @php $heroCopy = ['title' => 'Trek. Explore. Discover the Himalayas.', 'subtitle' => 'Trekking, expeditions, and cultural tours across Nepal, India, Bhutan, and Tibet.']; @endphp

    @if ($banners->isNotEmpty())
        <section class="relative text-white overflow-hidden"
                 x-data="{ active: 0, count: {{ $banners->count() }} }"
                 x-init="{{ $banners->count() > 1 ? 'setInterval(() => { active = (active + 1) % count }, 6000)' : '' }}">
            <div class="relative h-[60vh] min-h-[420px] max-h-[720px]">
                @foreach ($banners as $index => $banner)
                    <div x-show="active === {{ $index }}" x-cloak
                         x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         class="absolute inset-0 bg-secondary">
                        @if ($banner->media_type === 'video' && $banner->youtube_embed_url)
                            <iframe src="{{ $banner->youtube_embed_url }}"
                                    class="absolute inset-0 w-full h-full object-cover pointer-events-none"
                                    style="border:0;" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                        @elseif ($banner->file_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($banner->file_path) }}"
                                 alt="{{ $banner->title ?: $heroCopy['title'] }}" class="absolute inset-0 w-full h-full object-cover">
                        @endif

                        <div class="absolute inset-0 bg-black/40"></div>

                        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex flex-col items-center justify-center text-center">
                            <h1 class="text-3xl sm:text-5xl font-bold">{{ $heroCopy['title'] }}</h1>
                            <p class="mt-4 text-lg text-white/80 max-w-2xl mx-auto">{{ $heroCopy['subtitle'] }}</p>
                            <div class="mt-8">
                                <x-ui.button as="a" href="{{ route('tours.index') }}" size="lg">Browse Tours</x-ui.button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($banners->count() > 1)
                <div class="absolute bottom-4 inset-x-0 flex justify-center gap-2 z-20">
                    @foreach ($banners as $index => $banner)
                        <button type="button" @click="active = {{ $index }}" aria-label="Show slide {{ $index + 1 }}"
                                :class="active === {{ $index }} ? 'bg-white' : 'bg-white/40'" class="h-2 w-2 rounded-full transition"></button>
                    @endforeach
                </div>
            @endif
        </section>
    @else
        <section class="bg-secondary text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
                <h1 class="text-3xl sm:text-5xl font-bold">{{ $heroCopy['title'] }}</h1>
                <p class="mt-4 text-lg text-white/80 max-w-2xl mx-auto">{{ $heroCopy['subtitle'] }}</p>
                <div class="mt-8">
                    <x-ui.button as="a" href="{{ route('tours.index') }}" size="lg">Browse Tours</x-ui.button>
                </div>
            </div>
        </section>
    @endif

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
