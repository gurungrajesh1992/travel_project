<x-admin-layout title="Blog Categories">
    <x-slot name="header">Blog Categories</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.blog-posts.index') }}" size="sm" variant="secondary">View Posts</x-ui.button>
            <x-ui.button as="a" href="{{ route('admin.blog-categories.create') }}" size="sm">Add Category</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($blogCategories->isEmpty())
            <x-ui.empty-state title="No blog categories yet" description="Click &quot;Add Category&quot; to create the first one." />
        @else
            <x-ui.data-table :headers="['Name', 'Slug', 'Posts', '']">
                @foreach ($blogCategories as $blogCategory)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $blogCategory->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $blogCategory->slug }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $blogCategory->posts_count }}</td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.blog-categories.edit', $blogCategory) }}" class="text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.blog-categories.destroy', $blogCategory) }}" class="inline" onsubmit="return confirm('Delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$blogCategories" />
        @endif
    </x-ui.card>
</x-admin-layout>
