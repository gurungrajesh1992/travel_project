<x-admin-layout title="Categories">
    <x-slot name="header">Categories</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.categories.create') }}" size="sm">Add Category</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Categories..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($categories->isEmpty())
            <x-ui.empty-state title="No Categories yet" description="Click &quot;Add Category&quot; to create the first one." />
        @else
            <x-ui.data-table :headers="['Name', 'Parent', 'Status', '']">
                @foreach ($categories as $category)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $category->parent?->name ?? '—' }}</td>

                        <td class="px-4 py-3"><x-ui.badge :color="$category->status ? 'green' : 'gray'">{{ $category->status ? 'Active' : 'Inactive' }}</x-ui.badge></td>

                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Delete this Category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$categories" />
        @endif
    </x-ui.card>
</x-admin-layout>
