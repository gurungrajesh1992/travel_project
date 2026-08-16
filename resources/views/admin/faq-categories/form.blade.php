<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <x-ui.input label="Name" name="name" :value="$faqCategory->name ?? ''" required class="sm:col-span-2" />
    <x-ui.input label="Slug (optional)" name="slug" :value="$faqCategory->slug ?? ''" hint="Leave blank to auto-generate from name." class="sm:col-span-2" />

    <x-ui.input label="Sort Order" name="sort_order" type="number" :value="$faqCategory->sort_order ?? 0" />
</div>

<div class="mt-6 flex gap-3">
    <x-ui.button type="submit">Save</x-ui.button>
    <x-ui.button as="a" href="{{ route('admin.faq-categories.index') }}" variant="secondary">Cancel</x-ui.button>
</div>
