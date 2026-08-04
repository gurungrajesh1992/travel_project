<x-admin-layout title="Dashboard">
    <x-slot name="header">Dashboard</x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card label="Published Tours" :value="$publishedTourCount" :hint="$tourCount.' total'" />
        <x-ui.stat-card label="Bookings" :value="$bookingCount" :hint="$pendingBookingCount.' pending'" />
        <x-ui.stat-card label="Customers" :value="$customerCount" />
        <x-ui.stat-card label="Open Inquiries" :value="$openInquiryCount" />
    </div>

    <x-ui.card title="Recent Bookings">
        @if ($recentBookings->isEmpty())
            <x-ui.empty-state title="No bookings yet" description="New bookings will appear here as customers book tours." />
        @else
            <x-ui.data-table :headers="['Ref', 'Tour', 'Status', 'Payment', 'Total', '']">
                @foreach ($recentBookings as $booking)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $booking->booking_ref }}</td>
                        <td class="px-4 py-3">{{ $booking->tour->title }}</td>
                        <td class="px-4 py-3"><x-ui.badge color="primary">{{ ucfirst($booking->booking_status) }}</x-ui.badge></td>
                        <td class="px-4 py-3"><x-ui.badge>{{ ucfirst($booking->payment_status) }}</x-ui.badge></td>
                        <td class="px-4 py-3">{{ number_format($booking->total_amount, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary hover:underline">View</a>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>
        @endif
    </x-ui.card>
</x-admin-layout>
