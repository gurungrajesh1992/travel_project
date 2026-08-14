<x-admin-layout title="Gallery Items">
    <x-slot name="header">Gallery Items</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.gallery-categories.index') }}" size="sm" variant="secondary">Categories</x-ui.button>
            <x-ui.button as="a" href="{{ route('admin.gallery-items.create') }}" size="sm">Add Item</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by caption..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($galleryItems->isEmpty())
            <x-ui.empty-state title="No gallery items yet" description="Click &quot;Add Item&quot; to upload the first one." />
        @else
            <x-ui.data-table :headers="['Preview', 'Category', 'Type', 'Caption', 'Featured', '']">
                @foreach ($galleryItems as $galleryItem)
                    <tr>
                        <td class="px-4 py-3">
                            @if ($galleryItem->media_type === 'image' && $galleryItem->file_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($galleryItem->file_path) }}" class="h-12 w-16 object-cover rounded border border-gray-200">
                            @else
                                <span class="text-gray-400 text-xs">Video</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $galleryItem->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ ucfirst($galleryItem->media_type) }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $galleryItem->caption ?? '—' }}</td>
                        <td class="px-4 py-3"><x-ui.badge :color="$galleryItem->is_featured ? 'green' : 'gray'">{{ $galleryItem->is_featured ? 'Yes' : 'No' }}</x-ui.badge></td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.gallery-items.edit', $galleryItem) }}" class="text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.gallery-items.destroy', $galleryItem) }}" class="inline" onsubmit="return confirm('Delete this item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$galleryItems" />
        @endif
    </x-ui.card>
</x-admin-layout>
