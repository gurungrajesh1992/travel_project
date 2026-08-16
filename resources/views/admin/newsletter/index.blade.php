<x-admin-layout title="Newsletter Subscribers">
    <x-slot name="header">Newsletter Subscribers</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.newsletter.create') }}" size="sm">Compose &amp; Send</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subscribers..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($subscribers->isEmpty())
            <x-ui.empty-state title="No subscribers yet" description="Subscribers appear here once visitors sign up from the website footer." />
        @else
            <x-ui.data-table :headers="['Email', 'Subscribed', '']">
                @foreach ($subscribers as $subscriber)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $subscriber->email }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $subscriber->subscribed_at?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <form method="POST" action="{{ route('admin.newsletter.destroy', $subscriber) }}" class="inline" onsubmit="return confirm('Remove this subscriber?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$subscribers" />
        @endif
    </x-ui.card>
</x-admin-layout>
