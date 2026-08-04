<x-admin-layout title="Destinations">
    <x-slot name="header">Destinations</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.destinations.create') }}" size="sm">Add Destination</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Destinations..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($destinations->isEmpty())
            <x-ui.empty-state title="No Destinations yet" description="Click &quot;Add Destination&quot; to create the first one." />
        @else
            <x-ui.data-table :headers="['Name', 'Parent', 'Status', '']">
                @foreach ($destinations as $destination)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $destination->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $destination->parent?->name ?? '—' }}</td>

                        <td class="px-4 py-3"><x-ui.badge :color="$destination->status ? 'green' : 'gray'">{{ $destination->status ? 'Active' : 'Inactive' }}</x-ui.badge></td>

                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.destinations.edit', $destination) }}" class="text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.destinations.destroy', $destination) }}" class="inline" onsubmit="return confirm('Delete this Destination?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$destinations" />
        @endif
    </x-ui.card>
</x-admin-layout>
