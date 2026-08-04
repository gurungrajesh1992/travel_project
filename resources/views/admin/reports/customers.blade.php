@php $money = fn ($v) => $currency.' '.number_format($v, 2); @endphp

<x-admin-layout title="Customer Report">
    <x-slot name="header">Customer Report</x-slot>

    @include('admin.reports._tabs')
    @include('admin.reports._date-filter')

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card label="Customers" :value="$customerCount" :hint="$label" />
        <x-ui.stat-card label="Repeat Customers" :value="$repeatCustomers" />
        <x-ui.stat-card label="Total Revenue" :value="$money($totalRevenue)" />
        <x-ui.stat-card label="Avg. Bookings / Customer" :value="number_format($avgBookingsPerCustomer, 1)" />
    </div>

    <x-ui.card title="Top Customers" subtitle="Ranked by total spend in the selected period.">
        @if ($rows->isEmpty())
            <x-ui.empty-state title="No customers in this period" description="Try a wider date range." />
        @else
            <x-ui.data-table :headers="['Customer', 'Type', 'Bookings', 'Total Spent', 'Last Booking']">
                @foreach ($rows as $row)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $row->display_name }}</p>
                            @if ($row->email)
                                <p class="text-xs text-gray-500">{{ $row->email }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3"><x-ui.badge :color="$row->is_registered ? 'primary' : 'gray'">{{ $row->is_registered ? 'Registered' : 'Guest' }}</x-ui.badge></td>
                        <td class="px-4 py-3 text-gray-500">{{ $row->bookings_count }}</td>
                        <td class="px-4 py-3 text-gray-900 font-medium">{{ $money($row->total_spent) }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ \Illuminate\Support\Carbon::parse($row->last_booking_at)->format('M j, Y') }}</td>
                    </tr>
                @endforeach
            </x-ui.data-table>
        @endif
    </x-ui.card>
</x-admin-layout>
