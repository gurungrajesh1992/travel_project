<x-customer-layout title="Bookings">
    <x-slot name="header">My Bookings</x-slot>

    <x-ui.card>
        @if ($bookings->isEmpty())
            <x-ui.empty-state title="No bookings yet" description="Once you book a tour, it will show up here." />
        @else
            <x-ui.data-table :headers="['Booking Ref', 'Tour', 'Status', 'Payment', 'Total', '']">
                @foreach ($bookings as $booking)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $booking->booking_ref }}</td>
                        <td class="px-4 py-3">{{ $booking->tour->title }}</td>
                        <td class="px-4 py-3"><x-ui.badge :color="match($booking->booking_status) { 'confirmed' => 'green', 'cancelled' => 'red', 'completed' => 'blue', default => 'gray' }">{{ ucfirst($booking->booking_status) }}</x-ui.badge></td>
                        <td class="px-4 py-3"><x-ui.badge :color="match($booking->payment_status) { 'paid' => 'green', 'partial' => 'yellow', 'refunded' => 'red', default => 'gray' }">{{ ucfirst($booking->payment_status) }}</x-ui.badge></td>
                        <td class="px-4 py-3">{{ $booking->tour->currency }} {{ number_format($booking->total_amount, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('account.bookings.show', $booking) }}" class="text-primary hover:underline">View</a>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$bookings" />
        @endif
    </x-ui.card>
</x-customer-layout>
