<x-admin-layout title="Pages">
    <x-slot name="header">Pages</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.pages.create') }}" size="sm">Add Page</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search pages..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($pages->isEmpty())
            <x-ui.empty-state title="No pages yet" description="Click &quot;Add Page&quot; to create the first one." />
        @else
            <x-ui.data-table :headers="['Title', 'Slug', 'Status', '']">
                @foreach ($pages as $page)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $page->title }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $page->slug }}</td>
                        <td class="px-4 py-3">
                            <x-ui.badge :color="$page->status === 'published' ? 'green' : 'gray'">{{ ucfirst($page->status) }}</x-ui.badge>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.pages.edit', $page) }}" class="text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="inline" onsubmit="return confirm('Delete this page?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$pages" />
        @endif
    </x-ui.card>
</x-admin-layout>
