@php $mediaType = old('media_type', $galleryItem->media_type ?? 'image'); @endphp
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6" x-data="{ mediaType: '{{ $mediaType }}' }">
    <x-ui.select label="Category" name="gallery_category_id" :options="$categoryOptions" :selected="$galleryItem->gallery_category_id ?? null" placeholder="None" />
    <x-ui.select label="Related Tour (optional)" name="tour_id" :options="$tourOptions" :selected="$galleryItem->tour_id ?? null" placeholder="None" />

    <x-ui.select label="Media Type" name="media_type" :options="['image' => 'Image', 'video' => 'Video']" :selected="$mediaType" required x-model="mediaType" class="sm:col-span-2" />

    <div class="sm:col-span-2" x-show="mediaType === 'image'">
        <x-input-label for="file" value="Image" />
        <input type="file" name="file" id="file" accept="image/*" class="mt-1 block w-full text-sm">
        @if (!empty($galleryItem?->file_path))
            <img src="{{ \Illuminate\Support\Facades\Storage::url($galleryItem->file_path) }}" class="mt-2 h-20 w-32 object-cover rounded border border-gray-200">
        @endif
        <x-input-error :messages="$errors->get('file')" class="mt-1" />
    </div>

    <div class="sm:col-span-2" x-show="mediaType === 'video'">
        <x-ui.input label="Video URL" name="video_url" :value="$galleryItem->video_url ?? ''" />
    </div>

    <x-ui.input label="Caption" name="caption" :value="$galleryItem->caption ?? ''" class="sm:col-span-2" />
    <x-ui.input label="Sort Order" name="sort_order" type="number" :value="$galleryItem->sort_order ?? 0" />

    <div>
        <input type="hidden" name="is_featured" value="0">
        <x-ui.checkbox label="Featured" name="is_featured" :checked="$galleryItem->is_featured ?? false" />
    </div>
</div>

<div class="mt-6 flex gap-3">
    <x-ui.button type="submit">Save</x-ui.button>
    <x-ui.button as="a" href="{{ route('admin.gallery-items.index') }}" variant="secondary">Cancel</x-ui.button>
</div>
