<x-admin-layout title="FAQs">
    <x-slot name="header">FAQs</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.faq-categories.index') }}" size="sm" variant="secondary">Categories</x-ui.button>
            <x-ui.button as="a" href="{{ route('admin.faqs.create') }}" size="sm">Add FAQ</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search questions..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($faqs->isEmpty())
            <x-ui.empty-state title="No FAQs yet" description="Click &quot;Add FAQ&quot; to create the first one." />
        @else
            <x-ui.data-table :headers="['Question', 'Category', 'Status', '']">
                @foreach ($faqs as $faq)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $faq->question }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $faq->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3"><x-ui.badge :color="$faq->status ? 'green' : 'gray'">{{ $faq->status ? 'Active' : 'Inactive' }}</x-ui.badge></td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" class="inline" onsubmit="return confirm('Delete this FAQ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$faqs" />
        @endif
    </x-ui.card>
</x-admin-layout>
