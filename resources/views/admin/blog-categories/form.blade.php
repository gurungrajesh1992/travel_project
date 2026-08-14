<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <x-ui.input label="Name" name="name" :value="$blogCategory->name ?? ''" required class="sm:col-span-2" />
    <x-ui.input label="Slug (optional)" name="slug" :value="$blogCategory->slug ?? ''" hint="Leave blank to auto-generate from name." class="sm:col-span-2" />
</div>

<div class="mt-6 flex gap-3">
    <x-ui.button type="submit">Save</x-ui.button>
    <x-ui.button as="a" href="{{ route('admin.blog-categories.index') }}" variant="secondary">Cancel</x-ui.button>
</div>
