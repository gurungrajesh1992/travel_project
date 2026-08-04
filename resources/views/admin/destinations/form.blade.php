<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <x-ui.select label="Parent" name="parent_id" :options="$parentOptions" :selected="$destination->parent_id ?? null" placeholder="None (top-level)" class="sm:col-span-2" />

    <x-ui.input label="Name" name="name" :value="$destination->name ?? ''" required class="sm:col-span-2" />
    <x-ui.input label="Slug (optional)" name="slug" :value="$destination->slug ?? ''" hint="Leave blank to auto-generate from name." class="sm:col-span-2" />

    <x-ui.textarea label="Description" name="description" :value="$destination->description ?? ''" class="sm:col-span-2" />

    <div class="sm:col-span-2">
        <x-input-label for="thumbnail" value="Thumbnail" />
        <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="mt-1 block w-full text-sm">
        <p class="mt-1 text-xs text-gray-500">Shown behind the destination name under "Explore by Destination" on the website. Max 2MB.</p>
        @if (!empty($destination?->thumbnail))
            <img src="{{ \Illuminate\Support\Facades\Storage::url($destination->thumbnail) }}" class="mt-2 h-20 w-32 object-cover rounded border border-gray-200">
        @endif
        <x-input-error :messages="$errors->get('thumbnail')" class="mt-1" />
    </div>

    <x-ui.input label="Sort Order" name="sort_order" type="number" :value="$destination->sort_order ?? 0" />

    <div>
        <input type="hidden" name="status" value="0">
        <x-ui.checkbox label="Active" name="status" :checked="$destination->status ?? true" />
    </div>

</div>

<div class="mt-6 flex gap-3">
    <x-ui.button type="submit">Save</x-ui.button>
    <x-ui.button as="a" href="{{ route('admin.destinations.index') }}" variant="secondary">Cancel</x-ui.button>
</div>
