@php
    $selectedDestinations = old('destinations', ($tour ?? null)?->destinations->pluck('id')->all() ?? []);
    $selectedCategories = old('categories', ($tour ?? null)?->categories->pluck('id')->all() ?? []);
    $primaryDestination = old('primary_destination', (string) (($tour ?? null)?->primary_destination_id ?? ''));
    $primaryCategory = old('primary_category', (string) (($tour ?? null)?->primary_category_id ?? ''));
@endphp

<div class="space-y-6">
    <x-ui.card title="Basic Information" collapsible>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <x-ui.input label="Title" name="title" :value="$tour->title ?? ''" required class="sm:col-span-2" />
            <x-ui.input label="Slug (optional)" name="slug" :value="$tour->slug ?? ''" hint="Leave blank to auto-generate." class="sm:col-span-2" />
            <x-ui.input label="Short Description" name="short_description" :value="$tour->short_description ?? ''" class="sm:col-span-2" />
            <x-ui.textarea label="Full Description" name="full_description" :value="$tour->full_description ?? ''" rows="6" class="sm:col-span-2" />
        </div>
    </x-ui.card>

    <x-ui.card title="Destinations & Categories" subtitle="Check to include; use the Primary radio to choose which one is used for the canonical URL (and, for destinations, the nav's country ordering)." collapsible collapsed>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div x-data="{ selected: {{ json_encode(array_map('strval', $selectedDestinations)) }}, primary: '{{ $primaryDestination }}' }">
                <x-input-label value="Destinations" />
                <div class="mt-2 space-y-1 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                    @foreach ($destinations as $destination)
                        <div class="flex items-center justify-between gap-2 text-sm">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="destinations[]" value="{{ $destination->id }}"
                                       x-model="selected"
                                       @change="if (! $event.target.checked && primary === '{{ $destination->id }}') primary = ''"
                                       class="rounded border-gray-300 text-primary focus:ring-primary">
                                {{ $destination->name }}
                            </label>
                            <label class="flex items-center gap-1 text-xs text-gray-500">
                                <input type="radio" name="primary_destination" value="{{ $destination->id }}"
                                       x-model="primary"
                                       @click="if (! selected.includes('{{ $destination->id }}')) selected.push('{{ $destination->id }}')"
                                       class="text-primary focus:ring-primary">
                                Primary
                            </label>
                        </div>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('destinations')" class="mt-1" />
                <x-input-error :messages="$errors->get('primary_destination')" class="mt-1" />
            </div>

            <div x-data="{ selected: {{ json_encode(array_map('strval', $selectedCategories)) }}, primary: '{{ $primaryCategory }}' }">
                <x-input-label value="Categories" />
                <div class="mt-2 space-y-1 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                    @foreach ($categories as $category)
                        <div class="flex items-center justify-between gap-2 text-sm {{ $category->parent_id ? 'pl-4' : 'font-medium' }}">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                       x-model="selected"
                                       @change="if (! $event.target.checked && primary === '{{ $category->id }}') primary = ''"
                                       class="rounded border-gray-300 text-primary focus:ring-primary">
                                {{ $category->parent_id ? '— ' : '' }}{{ $category->name }}
                            </label>
                            <label class="flex items-center gap-1 text-xs text-gray-500 font-normal">
                                <input type="radio" name="primary_category" value="{{ $category->id }}"
                                       x-model="primary"
                                       @click="if (! selected.includes('{{ $category->id }}')) selected.push('{{ $category->id }}')"
                                       class="text-primary focus:ring-primary">
                                Primary
                            </label>
                        </div>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('categories')" class="mt-1" />
                <x-input-error :messages="$errors->get('primary_category')" class="mt-1" />
            </div>
        </div>
    </x-ui.card>

    <x-ui.card title="Trip Details" collapsible collapsed>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <x-ui.select label="Difficulty" name="difficulty_id" :options="$difficulties->pluck('name', 'id')" :selected="$tour->difficulty_id ?? null" placeholder="Select difficulty" />
            <x-ui.select label="Guide (optional)" name="guide_id" :options="$guides->pluck('name', 'id')" :selected="$tour->guide_id ?? null" placeholder="Unassigned" />
            <x-ui.input label="Max Altitude" name="max_altitude" :value="$tour->max_altitude ?? ''" />
            <x-ui.input label="Duration (days)" name="duration_days" type="number" :value="$tour->duration_days ?? ''" />
            <x-ui.input label="Duration (nights)" name="duration_nights" type="number" :value="$tour->duration_nights ?? ''" />
            <x-ui.input label="Best Season" name="best_season" :value="$tour->best_season ?? ''" />
            <x-ui.input label="Group Size Min" name="group_size_min" type="number" :value="$tour->group_size_min ?? 1" />
            <x-ui.input label="Group Size Max" name="group_size_max" type="number" :value="$tour->group_size_max ?? ''" />
            <x-ui.input label="Total Seats" name="total_seats" type="number" :value="$tour->total_seats ?? ''" />
        </div>
    </x-ui.card>

    <x-ui.card title="Pricing & Booking" collapsible collapsed>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <x-ui.input label="Base Price" name="base_price" type="number" step="0.01" :value="$tour->base_price ?? ''" required />
            <x-ui.input label="Currency" name="currency" :value="$tour->currency ?? 'USD'" required />
            <x-ui.select label="Booking Mode" name="booking_mode" :options="['instant' => 'Instant', 'inquiry' => 'Inquiry', 'both' => 'Both']" :selected="$tour->booking_mode ?? 'both'" required />
        </div>
    </x-ui.card>

    <x-ui.card title="Media & SEO" collapsible collapsed>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <x-input-label for="thumbnail" value="Thumbnail" />
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="mt-1 block w-full text-sm">
                @if (!empty($tour?->thumbnail))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($tour->thumbnail) }}" class="mt-2 h-20 rounded object-cover">
                @endif
                <x-input-error :messages="$errors->get('thumbnail')" class="mt-1" />
            </div>
            <x-ui.input label="Meta Title" name="meta_title" :value="$tour->meta_title ?? ''" class="sm:col-span-2" />
            <x-ui.textarea label="Meta Description" name="meta_description" :value="$tour->meta_description ?? ''" class="sm:col-span-2" />
        </div>
    </x-ui.card>

    <x-ui.card title="Map" subtitle="Pin the destination, or draw a trail route — shown on the tour's Map tab on the website." collapsible collapsed>
        <div id="tour-map-picker"
             data-initial-type="{{ $tour->map_type ?? 'point' }}"
             data-initial-data='{{ json_encode($tour->map_data ?? null) }}'>

            <div class="flex flex-wrap items-center gap-2 mb-3">
                <button type="button" data-map-mode="point" class="map-mode-btn px-3 py-1.5 text-sm rounded-md border border-gray-300 text-gray-600">Pin a Location</button>
                <button type="button" data-map-mode="route" class="map-mode-btn px-3 py-1.5 text-sm rounded-md border border-gray-300 text-gray-600">Draw a Trail Route</button>
                <button type="button" id="map-undo-btn" class="hidden px-3 py-1.5 text-sm rounded-md border border-gray-300 text-gray-600">Undo Last Point</button>
                <button type="button" id="map-clear-btn" class="px-3 py-1.5 text-sm rounded-md border border-red-300 text-red-600">Clear</button>
            </div>

            <div class="flex flex-wrap gap-2 mb-2">
                <input type="text" id="map-search-input" placeholder="Search a place (e.g. Everest Base Camp)"
                       class="flex-1 min-w-48 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
                <button type="button" id="map-search-btn" class="px-3 py-1.5 text-sm rounded-md border border-gray-300 text-gray-600">Search</button>
            </div>

            <p id="map-route-hint" class="hidden text-xs text-gray-500 mb-2">Click the map to add waypoints along the trail, in order. Use "Undo Last Point" to remove the most recent one.</p>

            <div id="tour-map-canvas" class="h-80 rounded-md border border-gray-200"></div>

            <input type="hidden" name="map_type" id="map-type-input" value="{{ $tour->map_type ?? 'point' }}">
            <input type="hidden" name="map_data" id="map-data-input" value='{{ json_encode($tour->map_data ?? null) }}'>
        </div>
        <x-input-error :messages="$errors->get('map_data')" class="mt-2" />
    </x-ui.card>

    <x-ui.card title="Status" collapsible collapsed>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <x-ui.select label="Status" name="status" :options="['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']" :selected="$tour->status ?? 'draft'" required />
            <div class="flex items-end pb-2">
                <input type="hidden" name="is_featured" value="0">
                <x-ui.checkbox label="Featured tour" name="is_featured" :checked="$tour->is_featured ?? false" />
            </div>
        </div>
    </x-ui.card>

    <div class="flex gap-3">
        <x-ui.button type="submit">Save Tour</x-ui.button>
        <x-ui.button as="a" href="{{ route('admin.tours.index') }}" variant="secondary">Cancel</x-ui.button>
    </div>
</div>
