@php $money = fn ($v) => $currency.' '.number_format($v, 2); @endphp

<x-admin-layout title="Sales & Revenue Report">
    <x-slot name="header">Sales & Revenue Report</x-slot>

    @vite(['resources/js/reports.js'])

    @include('admin.reports._tabs')
    @include('admin.reports._date-filter')

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card label="Total Revenue" :value="$money($totalRevenue)" :hint="$label" />
        <x-ui.stat-card label="Bookings" :value="$totalBookings" :hint="$label" />
        <x-ui.stat-card label="Avg. Booking Value" :value="$money($avgBookingValue)" />
        <x-ui.stat-card label="Discounts Given" :value="$money($totalDiscount)" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-ui.card title="Revenue Trend" class="lg:col-span-2">
            @if (empty($chartData['data']['labels']))
                <p class="text-sm text-gray-500">No revenue in this period.</p>
            @else
                <canvas data-chart="{{ json_encode($chartData) }}" height="110"></canvas>
            @endif
        </x-ui.card>

        <x-ui.card title="Payment Status Breakdown">
            @if ($paymentBreakdown->isEmpty())
                <p class="text-sm text-gray-500">No bookings in this period.</p>
            @else
                <div class="space-y-3">
                    @foreach ($paymentBreakdown as $row)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">{{ ucfirst($row->payment_status) }} ({{ $row->count }})</span>
                            <span class="font-medium text-gray-900">{{ $money($row->total) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>
</x-admin-layout>
