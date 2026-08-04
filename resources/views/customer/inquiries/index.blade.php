<x-customer-layout title="Inquiries">
    <x-slot name="header">My Inquiries</x-slot>

    <x-ui.card>
        @if ($inquiries->isEmpty())
            <x-ui.empty-state title="No inquiries yet" description="Questions you send about a tour will show up here." />
        @else
            <x-ui.data-table :headers="['Date', 'Tour', 'Subject', 'Status']">
                @foreach ($inquiries as $inquiry)
                    <tr>
                        <td class="px-4 py-3">{{ $inquiry->created_at?->format('M j, Y') }}</td>
                        <td class="px-4 py-3">{{ $inquiry->tour->title ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $inquiry->subject ?? '—' }}</td>
                        <td class="px-4 py-3"><x-ui.badge :color="match($inquiry->status) { 'responded' => 'green', 'closed' => 'gray', default => 'yellow' }">{{ ucfirst($inquiry->status) }}</x-ui.badge></td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$inquiries" />
        @endif
    </x-ui.card>
</x-customer-layout>
