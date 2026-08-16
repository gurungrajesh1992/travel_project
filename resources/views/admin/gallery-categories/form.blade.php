<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <x-ui.input label="Name" name="name" :value="$galleryCategory->name ?? ''" required class="sm:col-span-2" />
    <x-ui.input label="Slug (optional)" name="slug" :value="$galleryCategory->slug ?? ''" hint="Leave blank to auto-generate from name." class="sm:col-span-2" />

    <x-ui.input label="Sort Order" name="sort_order" type="number" :value="$galleryCategory->sort_order ?? 0" />

    <div>
        <input type="hidden" name="status" value="0">
        <x-ui.checkbox label="Active" name="status" :checked="$galleryCategory->status ?? true" />
    </div>
</div>

<div class="mt-6 flex gap-3">
    <x-ui.button type="submit">Save</x-ui.button>
    <x-ui.button as="a" href="{{ route('admin.gallery-categories.index') }}" variant="secondary">Cancel</x-ui.button>
</div>
