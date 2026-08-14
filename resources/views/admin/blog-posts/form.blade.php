<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <x-ui.input label="Title" name="title" :value="$blogPost->title ?? ''" required class="sm:col-span-2" />
    <x-ui.input label="Slug (optional)" name="slug" :value="$blogPost->slug ?? ''" hint="Leave blank to auto-generate from title." class="sm:col-span-2" />

    <x-ui.select label="Category" name="blog_category_id" :options="$categoryOptions" :selected="$blogPost->blog_category_id ?? null" placeholder="None" />
    <x-ui.select label="Status" name="status" :options="['draft' => 'Draft', 'published' => 'Published']" :selected="$blogPost->status ?? 'draft'" required />

    <x-ui.textarea label="Excerpt" name="excerpt" :value="$blogPost->excerpt ?? ''" :rows="2" class="sm:col-span-2" />
    <x-ui.textarea label="Content" name="content" :value="$blogPost->content ?? ''" :rows="10" required class="sm:col-span-2" />

    <div class="sm:col-span-2">
        <x-input-label for="featured_image" value="Featured Image" />
        <input type="file" name="featured_image" id="featured_image" accept="image/*" class="mt-1 block w-full text-sm">
        @if (!empty($blogPost?->featured_image))
            <img src="{{ \Illuminate\Support\Facades\Storage::url($blogPost->featured_image) }}" class="mt-2 h-20 w-32 object-cover rounded border border-gray-200">
        @endif
        <x-input-error :messages="$errors->get('featured_image')" class="mt-1" />
    </div>

    <x-ui.input label="Meta Title" name="meta_title" :value="$blogPost->meta_title ?? ''" class="sm:col-span-2" />
    <x-ui.textarea label="Meta Description" name="meta_description" :value="$blogPost->meta_description ?? ''" :rows="2" class="sm:col-span-2" />

    <x-ui.input label="Published At" name="published_at" type="datetime-local"
                :value="isset($blogPost->published_at) ? $blogPost->published_at->format('Y-m-d\TH:i') : ''" />
</div>

<div class="mt-6 flex gap-3">
    <x-ui.button type="submit">Save</x-ui.button>
    <x-ui.button as="a" href="{{ route('admin.blog-posts.index') }}" variant="secondary">Cancel</x-ui.button>
</div>
