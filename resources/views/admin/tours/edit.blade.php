<x-admin-layout title="Edit Tour">
    <x-slot name="header">Edit Tour &mdash; {{ $tour->title }}</x-slot>

    @vite(['resources/js/map-picker.js'])

    <div class="space-y-6">
        <form method="POST" action="{{ route('admin.tours.update', $tour) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.tours.form')
        </form>

        <x-ui.card title="Highlights" collapsible>
            <ul class="space-y-2 mb-4">
                @forelse ($tour->highlights as $highlight)
                    <li class="flex items-center justify-between text-sm border-b border-gray-100 pb-2">
                        <span>{{ $highlight->highlight_text }}</span>
                        <form method="POST" action="{{ route('admin.tours.highlights.destroy', [$tour, $highlight]) }}" onsubmit="return confirm('Remove this highlight?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Remove</button>
                        </form>
                    </li>
                @empty
                    <p class="text-sm text-gray-500">No highlights yet.</p>
                @endforelse
            </ul>
            <form method="POST" action="{{ route('admin.tours.highlights.store', $tour) }}" x-data="{ rows: [''] }" class="space-y-2">
                @csrf
                <template x-for="(row, i) in rows" :key="i">
                    <div class="flex gap-2">
                        <input type="text" :name="`highlight_text[${i}]`" x-model="rows[i]"
                               placeholder="e.g. Sunrise views from Kala Patthar"
                               class="flex-1 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
                        <button type="button" @click="rows.splice(i, 1)" x-show="rows.length > 1"
                                class="text-red-500 hover:text-red-700 px-2">&times;</button>
                    </div>
                </template>
                <div class="flex items-center gap-3">
                    <button type="button" @click="rows.push('')" class="text-sm text-primary hover:underline">+ Add another row</button>
                    <x-ui.button size="sm">Save Highlights</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Day-by-Day Itinerary" collapsible collapsed>
            <div class="space-y-4 mb-4">
                @forelse ($tour->itineraries as $itinerary)
                    <div class="border border-gray-200 rounded-md p-4">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-sm text-gray-900">Day {{ $itinerary->day_number }}: {{ $itinerary->area }}</p>
                            <form method="POST" action="{{ route('admin.tours.itineraries.destroy', [$tour, $itinerary]) }}" onsubmit="return confirm('Remove this day?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline text-sm">Remove</button>
                            </form>
                        </div>
                        @if ($itinerary->detail_itinerary)
                            <p class="text-sm text-gray-600 mt-1">{{ $itinerary->detail_itinerary }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">
                            {{ collect([$itinerary->transportation, $itinerary->meals, $itinerary->time ? $itinerary->time.' time' : null])->filter()->implode(' · ') }}
                        </p>

                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($itinerary->media as $media)
                                <div class="relative">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($media->file_path) }}" class="h-16 w-16 object-cover rounded">
                                    <form method="POST" action="{{ route('admin.tours.itineraries.media.destroy', [$tour, $itinerary, $media]) }}" class="absolute -top-2 -right-2">
                                        @csrf @method('DELETE')
                                        <button class="bg-red-600 text-white rounded-full h-5 w-5 text-xs">&times;</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                        <form method="POST" action="{{ route('admin.tours.itineraries.media.store', [$tour, $itinerary]) }}" enctype="multipart/form-data" class="mt-2 flex items-center gap-2">
                            @csrf
                            <input type="file" name="files[]" accept="image/*" multiple required class="text-xs">
                            <x-ui.button size="sm" variant="secondary">Add Photo(s)</x-ui.button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No itinerary days yet.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.tours.itineraries.store', $tour) }}"
                  x-data="{ rows: [{ day_number: '', area: '', transportation: '', time: '', detail_itinerary: '' }] }" class="space-y-3">
                @csrf
                <template x-for="(row, i) in rows" :key="i">
                    <div class="border border-gray-100 rounded-md p-3 space-y-2">
                        <div class="grid grid-cols-2 sm:grid-cols-6 gap-2">
                            <input type="number" :name="`day_number[${i}]`" x-model="row.day_number" placeholder="Day #"
                                   class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                            <input type="text" :name="`area[${i}]`" x-model="row.area" placeholder="Area"
                                   class="sm:col-span-2 rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                            <input type="text" :name="`transportation[${i}]`" x-model="row.transportation" placeholder="Transportation"
                                   class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                            <input type="text" :name="`time[${i}]`" x-model="row.time" placeholder="Time"
                                   class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                            <button type="button" @click="rows.splice(i, 1)" x-show="rows.length > 1"
                                    class="text-red-500 hover:text-red-700 text-sm">&times; Remove row</button>
                        </div>
                        <textarea :name="`detail_itinerary[${i}]`" x-model="row.detail_itinerary" placeholder="Detail Itinerary"
                                  class="w-full rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary" rows="2"></textarea>
                    </div>
                </template>
                <div class="flex items-center gap-3">
                    <button type="button" @click="rows.push({ day_number: '', area: '', transportation: '', time: '', detail_itinerary: '' })"
                            class="text-sm text-primary hover:underline">+ Add another day</button>
                    <x-ui.button size="sm">Save Days</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Includes & Excludes" collapsible collapsed>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach (['include' => 'Includes', 'exclude' => 'Excludes'] as $type => $label)
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">{{ $label }}</p>
                        <ul class="space-y-1 mb-3">
                            @forelse ($tour->costDetails->where('type', $type) as $item)
                                <li class="flex items-center justify-between text-sm">
                                    <span>{{ $item->detail_text }}</span>
                                    <form method="POST" action="{{ route('admin.tours.cost-details.destroy', [$tour, $item]) }}">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline">&times;</button>
                                    </form>
                                </li>
                            @empty
                                <li class="text-sm text-gray-400">None yet.</li>
                            @endforelse
                        </ul>
                        <form method="POST" action="{{ route('admin.tours.cost-details.store', $tour) }}" x-data="{ rows: [''] }" class="space-y-2">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <template x-for="(row, i) in rows" :key="i">
                                <div class="flex gap-2">
                                    <input type="text" :name="`detail_text[${i}]`" x-model="rows[i]" placeholder="Add item..."
                                           class="flex-1 rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                                    <button type="button" @click="rows.splice(i, 1)" x-show="rows.length > 1"
                                            class="text-red-500 hover:text-red-700 px-2">&times;</button>
                                </div>
                            </template>
                            <div class="flex items-center gap-3">
                                <button type="button" @click="rows.push('')" class="text-sm text-primary hover:underline">+ Add row</button>
                                <x-ui.button size="sm">Save</x-ui.button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card title="Gallery" collapsible collapsed>
            <div class="flex flex-wrap gap-3 mb-4">
                @forelse ($tour->media as $media)
                    <div class="relative">
                        @if ($media->media_type === 'image' && $media->file_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($media->file_path) }}" class="h-20 w-20 object-cover rounded">
                        @else
                            <div class="h-20 w-20 flex items-center justify-center bg-gray-100 rounded text-xs text-gray-500">Video</div>
                        @endif
                        <form method="POST" action="{{ route('admin.tours.media.destroy', [$tour, $media]) }}" class="absolute -top-2 -right-2">
                            @csrf @method('DELETE')
                            <button class="bg-red-600 text-white rounded-full h-5 w-5 text-xs">&times;</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No gallery items yet.</p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('admin.tours.media.store', $tour) }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2 mb-3">
                @csrf
                <input type="file" name="files[]" accept="image/*" multiple class="text-sm">
                <x-ui.button size="sm" variant="secondary">Add Photo(s)</x-ui.button>
            </form>
            <form method="POST" action="{{ route('admin.tours.media.store', $tour) }}" class="flex flex-wrap items-center gap-2">
                @csrf
                <input type="url" name="video_url" placeholder="Video URL (YouTube, Vimeo, ...)" class="flex-1 min-w-48 rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                <input type="text" name="caption" placeholder="Caption (optional)" class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                <x-ui.button size="sm" variant="secondary">Add Video</x-ui.button>
            </form>
        </x-ui.card>

        <x-ui.card title="FAQs" collapsible collapsed>
            <ul class="space-y-3 mb-4">
                @forelse ($tour->faqs as $faq)
                    <li class="border-b border-gray-100 pb-2">
                        <div class="flex items-start justify-between">
                            <p class="text-sm font-medium text-gray-900">{{ $faq->question }}</p>
                            <form method="POST" action="{{ route('admin.tours.faqs.destroy', [$tour, $faq]) }}">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline text-sm">Remove</button>
                            </form>
                        </div>
                        <p class="text-sm text-gray-600">{{ $faq->answer }}</p>
                    </li>
                @empty
                    <p class="text-sm text-gray-500">No FAQs yet.</p>
                @endforelse
            </ul>
            <form method="POST" action="{{ route('admin.tours.faqs.store', $tour) }}" x-data="{ rows: [{ question: '', answer: '' }] }" class="space-y-3">
                @csrf
                <template x-for="(row, i) in rows" :key="i">
                    <div class="border border-gray-100 rounded-md p-3 space-y-2">
                        <div class="flex items-start gap-2">
                            <input type="text" :name="`question[${i}]`" x-model="row.question" placeholder="Question"
                                   class="flex-1 rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                            <button type="button" @click="rows.splice(i, 1)" x-show="rows.length > 1"
                                    class="text-red-500 hover:text-red-700 text-sm shrink-0">&times; Remove</button>
                        </div>
                        <textarea :name="`answer[${i}]`" x-model="row.answer" placeholder="Answer" rows="2"
                                  class="w-full rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary"></textarea>
                    </div>
                </template>
                <div class="flex items-center gap-3">
                    <button type="button" @click="rows.push({ question: '', answer: '' })" class="text-sm text-primary hover:underline">+ Add another FAQ</button>
                    <x-ui.button size="sm">Save FAQs</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Departures" collapsible collapsed>
            <x-ui.data-table :headers="['Departure', 'Return', 'Seats', 'Status', '']">
                @forelse ($tour->departures as $departure)
                    <tr>
                        <td class="px-4 py-3">{{ $departure->departure_date->format('M j, Y') }}</td>
                        <td class="px-4 py-3">{{ $departure->return_date?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $departure->booked_seats }}/{{ $departure->available_seats }}</td>
                        <td class="px-4 py-3"><x-ui.badge :color="$departure->status === 'open' ? 'green' : 'gray'">{{ ucfirst($departure->status) }}</x-ui.badge></td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.tours.departures.destroy', [$tour, $departure]) }}">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-3 text-sm text-gray-500">No departures yet.</td></tr>
                @endforelse
            </x-ui.data-table>
            <form method="POST" action="{{ route('admin.tours.departures.store', $tour) }}" class="mt-4 space-y-2"
                  x-data="{ rows: [{ departure_date: '', return_date: '', available_seats: '', status: 'open' }] }">
                @csrf
                <template x-for="(row, i) in rows" :key="i">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        <input type="date" :name="`departure_date[${i}]`" x-model="row.departure_date"
                               class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                        <input type="date" :name="`return_date[${i}]`" x-model="row.return_date"
                               class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                        <input type="number" :name="`available_seats[${i}]`" x-model="row.available_seats" placeholder="Seats"
                               class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                        <select :name="`status[${i}]`" x-model="row.status" class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                            <option value="open">Open</option>
                            <option value="full">Full</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <button type="button" @click="rows.splice(i, 1)" x-show="rows.length > 1"
                                class="text-red-500 hover:text-red-700 text-sm text-left">&times; Remove</button>
                    </div>
                </template>
                <div class="flex items-center gap-3">
                    <button type="button" @click="rows.push({ departure_date: '', return_date: '', available_seats: '', status: 'open' })"
                            class="text-sm text-primary hover:underline">+ Add another departure</button>
                    <x-ui.button size="sm">Save Departures</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Seasonal Pricing" collapsible collapsed>
            <x-ui.data-table :headers="['Season', 'Dates', 'Price', '']">
                @forelse ($tour->seasonalPricing as $season)
                    <tr>
                        <td class="px-4 py-3">{{ $season->season_name }}</td>
                        <td class="px-4 py-3">{{ $season->start_date->format('M j') }} &ndash; {{ $season->end_date->format('M j, Y') }}</td>
                        <td class="px-4 py-3">{{ number_format($season->price, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.tours.seasonal-pricing.destroy', [$tour, $season]) }}">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-3 text-sm text-gray-500">No seasonal pricing yet.</td></tr>
                @endforelse
            </x-ui.data-table>
            <form method="POST" action="{{ route('admin.tours.seasonal-pricing.store', $tour) }}" class="mt-4 space-y-2"
                  x-data="{ rows: [{ season_name: '', start_date: '', end_date: '', price: '' }] }">
                @csrf
                <template x-for="(row, i) in rows" :key="i">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        <input type="text" :name="`season_name[${i}]`" x-model="row.season_name" placeholder="Season name"
                               class="sm:col-span-2 rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                        <input type="date" :name="`start_date[${i}]`" x-model="row.start_date"
                               class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                        <input type="date" :name="`end_date[${i}]`" x-model="row.end_date"
                               class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                        <input type="number" step="0.01" :name="`price[${i}]`" x-model="row.price" placeholder="Price"
                               class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                        <button type="button" @click="rows.splice(i, 1)" x-show="rows.length > 1"
                                class="text-red-500 hover:text-red-700 text-sm text-left">&times; Remove</button>
                    </div>
                </template>
                <div class="flex items-center gap-3">
                    <button type="button" @click="rows.push({ season_name: '', start_date: '', end_date: '', price: '' })"
                            class="text-sm text-primary hover:underline">+ Add another row</button>
                    <x-ui.button size="sm">Save Seasonal Pricing</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Pricing Tiers" subtitle="Group / child / private / solo pricing" collapsible collapsed>
            <x-ui.data-table :headers="['Type', 'Pax Range', 'Price / Person', '']">
                @forelse ($tour->pricingTiers as $tier)
                    <tr>
                        <td class="px-4 py-3">{{ ucfirst($tier->tier_type) }}</td>
                        <td class="px-4 py-3">{{ $tier->min_pax ?? '—' }} &ndash; {{ $tier->max_pax ?? '∞' }}</td>
                        <td class="px-4 py-3">{{ number_format($tier->price_per_person, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.tours.pricing-tiers.destroy', [$tour, $tier]) }}">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-3 text-sm text-gray-500">No pricing tiers yet.</td></tr>
                @endforelse
            </x-ui.data-table>
            <form method="POST" action="{{ route('admin.tours.pricing-tiers.store', $tour) }}" class="mt-4 space-y-2"
                  x-data="{ rows: [{ tier_type: 'group', min_pax: '', max_pax: '', price_per_person: '' }] }">
                @csrf
                <template x-for="(row, i) in rows" :key="i">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        <select :name="`tier_type[${i}]`" x-model="row.tier_type" class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                            <option value="group">Group</option>
                            <option value="child">Child</option>
                            <option value="private">Private</option>
                            <option value="solo">Solo</option>
                        </select>
                        <input type="number" :name="`min_pax[${i}]`" x-model="row.min_pax" placeholder="Min pax"
                               class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                        <input type="number" :name="`max_pax[${i}]`" x-model="row.max_pax" placeholder="Max pax"
                               class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                        <input type="number" step="0.01" :name="`price_per_person[${i}]`" x-model="row.price_per_person" placeholder="Price/person"
                               class="rounded-md border-gray-300 text-sm focus:border-primary focus:ring-primary">
                        <button type="button" @click="rows.splice(i, 1)" x-show="rows.length > 1"
                                class="text-red-500 hover:text-red-700 text-sm text-left">&times; Remove</button>
                    </div>
                </template>
                <div class="flex items-center gap-3">
                    <button type="button" @click="rows.push({ tier_type: 'group', min_pax: '', max_pax: '', price_per_person: '' })"
                            class="text-sm text-primary hover:underline">+ Add another tier</button>
                    <x-ui.button size="sm">Save Pricing Tiers</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-admin-layout>
