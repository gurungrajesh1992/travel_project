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

    @if ($trekCategories->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Explore Trek</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach ($trekCategories as $category)
                    <a href="{{ route('tours.index', ['category' => $category->slug]) }}" class="group relative overflow-hidden rounded-lg border border-gray-200 text-center hover:border-primary transition">
                        @if ($category->image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($category->image) }}" alt="{{ $category->name }}"
                                 class="absolute inset-0 h-full w-full object-cover">
                            <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition"></div>
                            <p class="relative z-10 flex items-center justify-center h-32 p-6 font-semibold text-white">{{ $category->name }}</p>
                        @else
                            <p class="flex items-center justify-center h-32 p-6 font-semibold group-hover:text-primary transition">{{ $category->name }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($featuredTours->isNotEmpty())
        <section class="bg-gray-50 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Explore Our Tours</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($featuredTours as $tour)
                        <x-website.tour-card :tour="$tour" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($latestReviews->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 uppercase tracking-wide">Latest Trip Review</h2>

            <div x-data="{
                    atStart: true,
                    atEnd: false,
                    updateEdges() {
                        const el = this.$refs.track;
                        this.atStart = el.scrollLeft <= 4;
                        this.atEnd = el.scrollLeft >= el.scrollWidth - el.clientWidth - 4;
                    },
                    slide(dir) {
                        const el = this.$refs.track;
                        const item = el.querySelector('[data-review-item]');
                        if (!item) return;
                        const gap = parseFloat(getComputedStyle(el).columnGap || 0);
                        el.scrollBy({ left: dir * (item.getBoundingClientRect().width + gap), behavior: 'smooth' });
                    },
                 }"
                 x-init="updateEdges()"
                 class="relative">
                <div x-ref="track" @scroll.debounce.100ms="updateEdges()"
                     class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                    @foreach ($latestReviews as $review)
                        <div data-review-item class="snap-start shrink-0 w-full sm:w-[calc(50%-0.75rem)] lg:w-[calc(25%-1.125rem)]">
                            <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm flex flex-col h-full">
                                <div class="flex items-center gap-3">
                                    @if ($review->user?->avatar)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($review->user->avatar) }}"
                                             alt="{{ $review->reviewer_name }}" class="h-10 w-10 rounded-full object-cover shrink-0">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-semibold shrink-0">
                                            {{ strtoupper(substr($review->reviewer_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $review->reviewer_name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $review->tour->title }}</p>
                                    </div>
                                </div>

                                <div class="mt-3 text-amber-400 text-sm leading-none" aria-label="{{ $review->rating }} out of 5 stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                    @endfor
                                </div>

                                <p class="mt-2 text-sm text-gray-600 line-clamp-4">{{ $review->review_text }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($latestReviews->count() > 1)
                    <button type="button" @click="slide(-1)" :disabled="atStart" aria-label="Previous review"
                            :class="atStart ? 'opacity-40 cursor-not-allowed' : 'text-gray-600 hover:text-primary hover:border-primary'"
                            class="absolute -left-4 top-1/2 -translate-y-1/2 h-9 w-9 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center transition">
                        &#8249;
                    </button>
                    <button type="button" @click="slide(1)" :disabled="atEnd" aria-label="Next review"
                            :class="atEnd ? 'opacity-40 cursor-not-allowed' : 'text-gray-600 hover:text-primary hover:border-primary'"
                            class="absolute -right-4 top-1/2 -translate-y-1/2 h-9 w-9 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center transition">
                        &#8250;
                    </button>
                @endif
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
