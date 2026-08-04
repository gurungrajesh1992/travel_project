@php
    $statusColors = ['pending' => 'yellow', 'confirmed' => 'primary', 'completed' => 'green', 'cancelled' => 'red'];
    $paymentColors = ['unpaid' => 'gray', 'partial' => 'yellow', 'paid' => 'green', 'refunded' => 'red'];
@endphp

<x-admin-layout title="Bookings">
    <x-slot name="header">Bookings</x-slot>

    <x-ui.card>
        <form method="GET" class="mb-4 flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ref, guest, tour..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">

            <select name="booking_status" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
                <option value="">All statuses</option>
                @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('booking_status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>

            <select name="payment_status" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
                <option value="">All payments</option>
                @foreach (['unpaid', 'partial', 'paid', 'refunded'] as $status)
                    <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>

            <x-ui.button type="submit" variant="secondary" size="sm">Filter</x-ui.button>
        </form>

        @if ($bookings->isEmpty())
            <x-ui.empty-state title="No bookings found" description="Bookings submitted from the website will show up here." />
        @else
            <x-ui.data-table :headers="['Ref', 'Customer', 'Tour', 'Total', 'Status', 'Payment', 'Booked', '']">
                @foreach ($bookings as $booking)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $booking->booking_ref }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $booking->customerName() }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $booking->tour->title }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $booking->tour->currency }} {{ number_format($booking->total_amount, 2) }}</td>
                        <td class="px-4 py-3"><x-ui.badge :color="$statusColors[$booking->booking_status] ?? 'gray'">{{ ucfirst($booking->booking_status) }}</x-ui.badge></td>
                        <td class="px-4 py-3"><x-ui.badge :color="$paymentColors[$booking->payment_status] ?? 'gray'">{{ ucfirst($booking->payment_status) }}</x-ui.badge></td>
                        <td class="px-4 py-3 text-gray-500">{{ $booking->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary hover:underline">View</a>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$bookings" />
        @endif
    </x-ui.card>
</x-admin-layout>
