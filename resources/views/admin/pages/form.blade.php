<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <x-ui.input label="Title" name="title" :value="$page->title ?? ''" required class="sm:col-span-2" />
    <x-ui.input label="Slug (optional)" name="slug" :value="$page->slug ?? ''" hint="Leave blank to auto-generate from title." class="sm:col-span-2" />

    <x-ui.select label="Status" name="status" :options="['draft' => 'Draft', 'published' => 'Published']" :selected="$page->status ?? 'published'" required />

    <x-ui.textarea label="Content" name="content" :value="$page->content ?? ''" :rows="10" class="sm:col-span-2" />

    <div class="sm:col-span-2">
        <x-input-label for="featured_image" value="Featured Image" />
        <input type="file" name="featured_image" id="featured_image" accept="image/*" class="mt-1 block w-full text-sm">
        @if (!empty($page?->featured_image))
            <img src="{{ \Illuminate\Support\Facades\Storage::url($page->featured_image) }}" class="mt-2 h-20 w-32 object-cover rounded border border-gray-200">
        @endif
        <x-input-error :messages="$errors->get('featured_image')" class="mt-1" />
    </div>

    <x-ui.input label="Meta Title" name="meta_title" :value="$page->meta_title ?? ''" class="sm:col-span-2" />
    <x-ui.textarea label="Meta Description" name="meta_description" :value="$page->meta_description ?? ''" :rows="2" class="sm:col-span-2" />
</div>

<div class="mt-6 flex gap-3">
    <x-ui.button type="submit">Save</x-ui.button>
    <x-ui.button as="a" href="{{ route('admin.pages.index') }}" variant="secondary">Cancel</x-ui.button>
</div>
