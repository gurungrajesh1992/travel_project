<x-admin-layout title="FAQ Categories">
    <x-slot name="header">FAQ Categories</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.faqs.index') }}" size="sm" variant="secondary">View FAQs</x-ui.button>
            <x-ui.button as="a" href="{{ route('admin.faq-categories.create') }}" size="sm">Add Category</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($faqCategories->isEmpty())
            <x-ui.empty-state title="No FAQ categories yet" description="Click &quot;Add Category&quot; to create the first one." />
        @else
            <x-ui.data-table :headers="['Name', 'FAQs', '']">
                @foreach ($faqCategories as $faqCategory)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $faqCategory->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $faqCategory->faqs_count }}</td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.faq-categories.edit', $faqCategory) }}" class="text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.faq-categories.destroy', $faqCategory) }}" class="inline" onsubmit="return confirm('Delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$faqCategories" />
        @endif
    </x-ui.card>
</x-admin-layout>
