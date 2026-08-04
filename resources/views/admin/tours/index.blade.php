<x-admin-layout title="Tours">
    <x-slot name="header">Tours</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.tours.create') }}" size="sm">Add Tour</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4 flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tours..."
                   class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">

            <select name="status" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary" onchange="this.form.submit()">
                <option value="">All statuses</option>
                @foreach (['draft', 'published', 'archived'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>

            <select name="destination" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary" onchange="this.form.submit()">
                <option value="">All destinations</option>
                @foreach ($destinations as $destination)
                    <option value="{{ $destination->id }}" @selected((string) request('destination') === (string) $destination->id)>{{ $destination->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="text-sm text-primary hover:underline">Filter</button>
        </form>

        @if ($tours->isEmpty())
            <x-ui.empty-state title="No tours yet" description="Click &quot;Add Tour&quot; to create the first one." />
        @else
            <x-ui.data-table :headers="['Title', 'Destination', 'Category', 'Price', 'Status', '']">
                @foreach ($tours as $tour)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $tour->title }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $tour->primaryDestination?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $tour->primaryCategory?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $tour->currency }} {{ number_format($tour->base_price, 2) }}</td>
                        <td class="px-4 py-3">
                            <x-ui.badge :color="match($tour->status) { 'published' => 'green', 'archived' => 'gray', default => 'yellow' }">
                                {{ ucfirst($tour->status) }}
                            </x-ui.badge>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.tours.edit', $tour) }}" class="text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.tours.destroy', $tour) }}" class="inline" onsubmit="return confirm('Delete this tour?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$tours" />
        @endif
    </x-ui.card>
</x-admin-layout>
