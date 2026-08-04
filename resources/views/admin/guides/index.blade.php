<x-admin-layout title="Guides">
    <x-slot name="header">Guides</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.guides.create') }}" size="sm">Add Guide</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search guides..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($guides->isEmpty())
            <x-ui.empty-state title="No Guides yet" description="Click &quot;Add Guide&quot; to create the first one." />
        @else
            <x-ui.data-table :headers="['Guide', 'Languages', 'Experience', 'Bookings', 'Status', '']">
                @foreach ($guides as $guide)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($guide->photo)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($guide->photo) }}" class="h-8 w-8 rounded-full object-cover">
                                @else
                                    <span class="h-8 w-8 rounded-full bg-gray-200"></span>
                                @endif
                                <span class="font-medium text-gray-900">{{ $guide->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $guide->languages ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $guide->experience_years ? $guide->experience_years.' yrs' : '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $guide->bookings_count }}</td>
                        <td class="px-4 py-3"><x-ui.badge :color="$guide->status ? 'green' : 'gray'">{{ $guide->status ? 'Active' : 'Inactive' }}</x-ui.badge></td>
                        <td class="px-4 py-3 text-right space-x-3">
                            @if ($guide->status)
                                <a href="{{ route('guides.show', $guide) }}" target="_blank" class="text-gray-500 hover:underline">View</a>
                            @endif
                            <a href="{{ route('admin.guides.edit', $guide) }}" class="text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.guides.destroy', $guide) }}" class="inline" onsubmit="return confirm('Delete this Guide?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$guides" />
        @endif
    </x-ui.card>
</x-admin-layout>
