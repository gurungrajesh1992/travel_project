@php
    $statusColors = ['new' => 'yellow', 'responded' => 'green', 'closed' => 'gray'];
@endphp

<x-admin-layout title="Inquiries">
    <x-slot name="header">Inquiries</x-slot>

    <x-ui.card>
        <form method="GET" class="mb-4 flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, subject..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">

            <select name="status" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
                <option value="">All statuses</option>
                @foreach (['new', 'responded', 'closed'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>

            <x-ui.button type="submit" variant="secondary" size="sm">Filter</x-ui.button>
        </form>

        @if ($inquiries->isEmpty())
            <x-ui.empty-state title="No inquiries yet" description="Messages submitted from tour pages or the Contact Us form will show up here." />
        @else
            <x-ui.data-table :headers="['From', 'Tour', 'Subject', 'Received', 'Status', '']">
                @foreach ($inquiries as $inquiry)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $inquiry->name }}</p>
                            <p class="text-xs text-gray-500">{{ $inquiry->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $inquiry->tour?->title ?? 'General' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $inquiry->subject ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $inquiry->created_at?->format('M j, Y g:i A') }}</td>
                        <td class="px-4 py-3"><x-ui.badge :color="$statusColors[$inquiry->status] ?? 'gray'">{{ ucfirst($inquiry->status) }}</x-ui.badge></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="text-primary hover:underline">View</a>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$inquiries" />
        @endif
    </x-ui.card>
</x-admin-layout>
