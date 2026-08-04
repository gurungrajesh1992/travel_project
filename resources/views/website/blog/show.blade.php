<x-website-layout :title="$post->title">
    <article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if ($post->category)
            <p class="text-xs font-medium text-primary uppercase">{{ $post->category->name }}</p>
        @endif
        <h1 class="text-3xl font-bold text-gray-900 mt-1">{{ $post->title }}</h1>
        <p class="text-sm text-gray-400 mt-2">{{ $post->published_at?->format('F j, Y') }} &middot; {{ $post->author->name }}</p>

        @if ($post->featured_image)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}" class="w-full rounded-lg mt-6">
        @endif

        <div class="prose max-w-none mt-8">
            {!! $post->content !!}
        </div>

        <a href="{{ route('blog.index') }}" class="inline-block mt-8 text-primary hover:underline">&larr; Back to Blog</a>
    </article>
</x-website-layout>
