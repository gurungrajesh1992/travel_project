<x-website-layout title="Blog">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Blog</h1>

        @if ($posts->isEmpty())
            <x-ui.empty-state title="No posts yet" description="Check back soon." />
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($posts as $post)
                    <a href="{{ route('blog.show', $post) }}" class="block bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition">
                        @if ($post->featured_image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}" class="h-40 w-full object-cover">
                        @endif
                        <div class="p-5">
                            @if ($post->category)
                                <p class="text-xs font-medium text-primary uppercase">{{ $post->category->name }}</p>
                            @endif
                            <p class="font-semibold text-gray-900 mt-1">{{ $post->title }}</p>
                            <p class="text-sm text-gray-500 mt-2">{{ $post->excerpt }}</p>
                            <p class="text-xs text-gray-400 mt-3">{{ $post->published_at?->format('M j, Y') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
        @endif
    </div>
</x-website-layout>
