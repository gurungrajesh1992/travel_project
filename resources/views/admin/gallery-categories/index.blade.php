<x-admin-layout title="Gallery Categories">
    <x-slot name="header">Gallery Categories</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.gallery-items.index') }}" size="sm" variant="secondary">View Items</x-ui.button>
            <x-ui.button as="a" href="{{ route('admin.gallery-categories.create') }}" size="sm">Add Category</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($galleryCategories->isEmpty())
            <x-ui.empty-state title="No gallery categories yet" description="Click &quot;Add Category&quot; to create the first one." />
        @else
            <x-ui.data-table :headers="['Name', 'Items', 'Status', '']">
                @foreach ($galleryCategories as $galleryCategory)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $galleryCategory->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $galleryCategory->items_count }}</td>
                        <td class="px-4 py-3"><x-ui.badge :color="$galleryCategory->status ? 'green' : 'gray'">{{ $galleryCategory->status ? 'Active' : 'Inactive' }}</x-ui.badge></td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.gallery-categories.edit', $galleryCategory) }}" class="text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.gallery-categories.destroy', $galleryCategory) }}" class="inline" onsubmit="return confirm('Delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$galleryCategories" />
        @endif
    </x-ui.card>
</x-admin-layout>
