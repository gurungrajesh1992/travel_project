<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <x-ui.select label="Parent" name="parent_id" :options="$parentOptions" :selected="$category->parent_id ?? null" placeholder="None (top-level)" class="sm:col-span-2" />

    <x-ui.input label="Name" name="name" :value="$category->name ?? ''" required class="sm:col-span-2" />
    <x-ui.input label="Slug (optional)" name="slug" :value="$category->slug ?? ''" hint="Leave blank to auto-generate from name." class="sm:col-span-2" />

    <x-ui.textarea label="Description" name="description" :value="$category->description ?? ''" class="sm:col-span-2" />

    <x-ui.input label="Sort Order" name="sort_order" type="number" :value="$category->sort_order ?? 0" />

    <div>
        <input type="hidden" name="status" value="0">
        <x-ui.checkbox label="Active" name="status" :checked="$category->status ?? true" />
    </div>

</div>

<div class="mt-6 flex gap-3">
    <x-ui.button type="submit">Save</x-ui.button>
    <x-ui.button as="a" href="{{ route('admin.categories.index') }}" variant="secondary">Cancel</x-ui.button>
</div>
