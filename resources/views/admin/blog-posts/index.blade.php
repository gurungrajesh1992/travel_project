<x-admin-layout title="Blog Posts">
    <x-slot name="header">Blog Posts</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.blog-categories.index') }}" size="sm" variant="secondary">Categories</x-ui.button>
            <x-ui.button as="a" href="{{ route('admin.blog-posts.create') }}" size="sm">Add Post</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search posts..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($blogPosts->isEmpty())
            <x-ui.empty-state title="No blog posts yet" description="Click &quot;Add Post&quot; to create the first one." />
        @else
            <x-ui.data-table :headers="['Title', 'Category', 'Status', 'Published', '']">
                @foreach ($blogPosts as $blogPost)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $blogPost->title }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $blogPost->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <x-ui.badge :color="$blogPost->status === 'published' ? 'green' : 'gray'">{{ ucfirst($blogPost->status) }}</x-ui.badge>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $blogPost->published_at?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.blog-posts.edit', $blogPost) }}" class="text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.blog-posts.destroy', $blogPost) }}" class="inline" onsubmit="return confirm('Delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$blogPosts" />
        @endif
    </x-ui.card>
</x-admin-layout>
