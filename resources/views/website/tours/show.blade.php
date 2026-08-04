<x-website-layout :title="$tour->title">
    @vite(['resources/js/tour-map.js'])

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ tab: 'description', mainImage: {{ $tour->thumbnail ? "'".\Illuminate\Support\Facades\Storage::url($tour->thumbnail)."'" : 'null' }} }">

        <p class="text-sm text-gray-500">
            @if ($tour->destinations->count() <= 1)
                <a href="{{ route('destinations.show', $tour->primaryDestination) }}" class="hover:text-primary">{{ $tour->destinationsLabel() }}</a>
            @else
                {{ $tour->destinationsLabel() }}
            @endif
            @if ($tour->categoriesLabel()) / {{ $tour->categoriesLabel() }} @endif
        </p>
        <h1 class="text-3xl font-bold text-gray-900 mt-1">{{ $tour->title }}</h1>
        <p class="mt-2 text-gray-600 max-w-3xl">{{ $tour->short_description }}</p>

        @if ($avgRating)
            <p class="mt-2 text-sm text-yellow-600">{{ str_repeat('★', round($avgRating)) }}{{ str_repeat('☆', 5 - round($avgRating)) }} {{ number_format($avgRating, 1) }} ({{ $tour->approvedReviews->count() }} reviews)</p>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mt-8">
            <div class="lg:col-span-2">
                {{-- Gallery --}}
                <div class="h-80 bg-gray-100 rounded-lg overflow-hidden">
                    <template x-if="mainImage">
                        <img :src="mainImage" class="h-full w-full object-cover">
                    </template>
                    <template x-if="!mainImage">
                        <div class="h-full w-full flex items-center justify-center text-gray-400">No image</div>
                    </template>
                </div>
                @if ($tour->media->isNotEmpty())
                    <div class="flex gap-2 mt-2 overflow-x-auto">
                        @foreach ($tour->media as $media)
                            @if ($media->file_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($media->file_path) }}"
                                     @click="mainImage = '{{ \Illuminate\Support\Facades\Storage::url($media->file_path) }}'"
                                     class="h-16 w-16 object-cover rounded cursor-pointer border-2 border-transparent hover:border-primary">
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Tabs --}}
                <div class="mt-8 border-b border-gray-200">
                    <nav class="flex gap-6 flex-wrap">
                        @foreach (['description' => 'Description', 'availability' => 'Availability', 'itinerary' => 'Itinerary', 'map' => 'Map', 'includes' => 'Includes/Excludes', 'reviews' => 'Reviews'] as $key => $label)
                            <button @click="tab = '{{ $key }}'"
                                    :class="tab === '{{ $key }}' ? 'border-primary text-primary' : 'border-transparent text-gray-500'"
                                    class="pb-3 border-b-2 text-sm font-medium">
                                {{ $label }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                <div class="py-6">
                    <div x-show="tab === 'description'" x-cloak class="prose max-w-none">
                        <p>{{ $tour->full_description }}</p>
                        @if ($tour->highlights->isNotEmpty())
                            <h3 class="font-semibold mt-4">Highlights</h3>
                            <ul>
                                @foreach ($tour->highlights as $highlight)
                                    <li>{{ $highlight->highlight_text }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div x-show="tab === 'availability'" x-cloak>
                        @if ($tour->departures->isEmpty())
                            <p class="text-sm text-gray-500">No upcoming departures — contact us for custom dates.</p>
                        @else
                            <x-ui.data-table :headers="['Departure', 'Return', 'Seats left', 'Status']">
                                @foreach ($tour->departures as $departure)
                                    <tr>
                                        <td class="px-4 py-3">{{ $departure->departure_date->format('M j, Y') }}</td>
                                        <td class="px-4 py-3">{{ $departure->return_date?->format('M j, Y') ?? '—' }}</td>
                                        <td class="px-4 py-3">{{ $departure->remainingSeats() }}</td>
                                        <td class="px-4 py-3"><x-ui.badge color="green">{{ ucfirst($departure->status) }}</x-ui.badge></td>
                                    </tr>
                                @endforeach
                            </x-ui.data-table>
                        @endif
                    </div>

                    <div x-show="tab === 'itinerary'" x-cloak class="space-y-4">
                        @forelse ($tour->itineraries as $itinerary)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <p class="font-semibold text-gray-900">Day {{ $itinerary->day_number }}: {{ $itinerary->title }}</p>
                                @if ($itinerary->description)
                                    <p class="text-sm text-gray-600 mt-1">{{ $itinerary->description }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-2">
                                    {{ collect([$itinerary->accommodation, $itinerary->meals, $itinerary->walking_hours ? $itinerary->walking_hours.' walking' : null])->filter()->implode(' · ') }}
                                </p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Itinerary coming soon.</p>
                        @endforelse
                    </div>

                    <div x-show="tab === 'map'" x-cloak>
                        @if ($tour->map_type && $tour->map_data)
                            <div data-tour-map="{{ json_encode($tour->map_data) }}" data-tour-map-type="{{ $tour->map_type }}" class="w-full h-80 rounded-lg border border-gray-200"></div>
                        @else
                            <p class="text-sm text-gray-500">Map not available for this tour.</p>
                        @endif
                    </div>

                    <div x-show="tab === 'includes'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <p class="font-semibold text-gray-900 mb-2">Includes</p>
                            <ul class="text-sm text-gray-600 space-y-1">
                                @forelse ($tour->includes as $item)
                                    <li>✓ {{ $item->detail_text }}</li>
                                @empty
                                    <li class="text-gray-400">Not specified.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 mb-2">Excludes</p>
                            <ul class="text-sm text-gray-600 space-y-1">
                                @forelse ($tour->excludes as $item)
                                    <li>✕ {{ $item->detail_text }}</li>
                                @empty
                                    <li class="text-gray-400">Not specified.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div x-show="tab === 'reviews'" x-cloak>
                        <div class="space-y-4 mb-8">
                            @forelse ($tour->approvedReviews as $review)
                                <div class="border-b border-gray-100 pb-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $review->reviewer_name }} @if($review->reviewer_country) &middot; {{ $review->reviewer_country }} @endif</p>
                                    <p class="text-yellow-600 text-sm">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $review->review_text }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No reviews yet — be the first!</p>
                            @endforelse
                        </div>

                        <x-ui.card title="Write a Review">
                            <form method="POST" action="{{ route('tours.reviews.store', $tour) }}" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <x-ui.input label="Name" name="reviewer_name" required />
                                    <x-ui.input label="Country (optional)" name="reviewer_country" />
                                </div>
                                <x-ui.select label="Rating" name="rating" :options="[5 => '5 - Excellent', 4 => '4 - Good', 3 => '3 - Average', 2 => '2 - Poor', 1 => '1 - Terrible']" required />
                                <x-ui.textarea label="Review" name="review_text" required />
                                <x-ui.button type="submit">Submit Review</x-ui.button>
                            </form>
                        </x-ui.card>
                    </div>
                </div>

                @if ($tour->faqs->isNotEmpty())
                    <div class="mt-8">
                        <h3 class="font-semibold text-gray-900 mb-3">Frequently Asked Questions</h3>
                        <div class="space-y-2" x-data="{ open: null }">
                            @foreach ($tour->faqs as $faq)
                                <div class="border border-gray-200 rounded-md">
                                    <button @click="open = open === {{ $faq->id }} ? null : {{ $faq->id }}" class="w-full text-left px-4 py-3 text-sm font-medium text-gray-900">
                                        {{ $faq->question }}
                                    </button>
                                    <div x-show="open === {{ $faq->id }}" x-cloak class="px-4 pb-3 text-sm text-gray-600">{{ $faq->answer }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Booking sidebar --}}
            <div>
                <div class="sticky top-6 space-y-6">
                    <x-ui.card title="Book This Tour">
                        <p class="text-2xl font-bold text-gray-900">{{ $tour->currency }} {{ number_format($tour->base_price, 0) }} <span class="text-sm font-normal text-gray-500">/ person</span></p>

                        <form method="POST" action="{{ route('tours.book', $tour) }}" class="mt-4 space-y-3">
                            @csrf
                            <input type="hidden" name="booking_type" value="instant">

                            @if ($tour->departures->isNotEmpty())
                                <x-ui.select name="departure_id" label="Departure Date" :options="$tour->departures->mapWithKeys(fn($d) => [$d->id => $d->departure_date->format('M j, Y').' ('.$d->remainingSeats().' seats left)'])" placeholder="Flexible / contact us" />
                            @endif

                            @if ($tour->pricingTiers->isNotEmpty())
                                <x-ui.select name="pricing_tier_id" label="Pricing Tier" :options="$tour->pricingTiers->mapWithKeys(fn($t) => [$t->id => ucfirst($t->tier_type).' - '.$tour->currency.' '.number_format($t->price_per_person, 0).'/person'])" placeholder="Standard price" />
                            @endif

                            <div class="grid grid-cols-2 gap-3">
                                <x-ui.input label="Adults" name="num_adults" type="number" value="1" required />
                                <x-ui.input label="Children" name="num_children" type="number" value="0" />
                            </div>

                            @guest
                                <x-ui.input label="Full Name" name="guest_name" required />
                                <x-ui.input label="Email" name="guest_email" type="email" required />
                                <x-ui.input label="Phone" name="guest_phone" />
                            @endguest

                            @if ($tour->guide)
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="guide_id" value="{{ $tour->guide->id }}" class="rounded border-gray-300 text-primary focus:ring-primary">
                                    Request guide:
                                    <a href="{{ route('guides.show', $tour->guide) }}" target="_blank" class="text-primary hover:underline" @click.stop>{{ $tour->guide->name }}</a>
                                </label>
                            @endif

                            <x-ui.input label="Coupon Code (optional)" name="coupon_code" />
                            <x-ui.textarea label="Special Requests" name="special_requests" rows="2" />

                            <x-ui.button type="submit" class="w-full justify-center">Book Now</x-ui.button>
                        </form>
                    </x-ui.card>

                    <x-ui.card title="Have a Question?">
                        <form method="POST" action="{{ route('tours.inquiries.store', $tour) }}" class="space-y-3">
                            @csrf
                            <x-ui.input label="Name" name="name" required />
                            <x-ui.input label="Email" name="email" type="email" required />
                            <x-ui.textarea label="Message" name="message" required />
                            <x-ui.button type="submit" variant="secondary" class="w-full justify-center">Send Inquiry</x-ui.button>
                        </form>
                    </x-ui.card>
                </div>
            </div>
        </div>

        @if ($relatedTours->isNotEmpty())
            <div class="mt-16">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">You Might Also Like</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach ($relatedTours as $related)
                        <x-website.tour-card :tour="$related" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-website-layout>
