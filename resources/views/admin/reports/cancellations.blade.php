@php $money = fn ($v) => $currency.' '.number_format($v, 2); @endphp

<x-admin-layout title="Cancellation Report">
    <x-slot name="header">Cancellation Report</x-slot>

    @vite(['resources/js/reports.js'])

    @include('admin.reports._tabs')
    @include('admin.reports._date-filter')

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card label="Cancelled Bookings" :value="$cancelledCount" :hint="$label" />
        <x-ui.stat-card label="Cancellation Rate" :value="$cancellationRate.'%'" :hint="$totalBookings.' bookings made'" />
        <x-ui.stat-card label="Lost Revenue" :value="$money($lostRevenue)" />
        <x-ui.stat-card label="Top Reason" :value="$topReasons->first()['reason'] ?? '—'" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <x-ui.card title="Cancellations Trend" class="lg:col-span-2">
            @if (empty($chartData['data']['labels']))
                <p class="text-sm text-gray-500">No cancellations in this period.</p>
            @else
                <canvas data-chart="{{ json_encode($chartData) }}" height="110"></canvas>
            @endif
        </x-ui.card>

        <x-ui.card title="Top Reasons">
            @if ($topReasons->isEmpty())
                <p class="text-sm text-gray-500">No cancellations in this period.</p>
            @else
                <div class="space-y-3">
                    @foreach ($topReasons as $reason)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 truncate pr-2">{{ $reason['reason'] }}</span>
                            <span class="font-medium text-gray-900 shrink-0">{{ $reason['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>

    <x-ui.card title="Cancelled Bookings">
        @if ($cancelledBookings->isEmpty())
            <x-ui.empty-state title="No cancellations in this period" description="Nothing was cancelled in the selected date range." />
        @else
            <x-ui.data-table :headers="['Ref', 'Customer', 'Tour', 'Amount', 'Cancelled On', 'Reason']">
                @foreach ($cancelledBookings as $booking)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="hover:text-primary">{{ $booking->booking_ref }}</a>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $booking->customerName() }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $booking->tour?->title ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $money($booking->total_amount) }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $booking->cancelled_at?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 max-w-xs truncate" title="{{ $booking->cancellation_reason }}">{{ $booking->cancellation_reason ?? '—' }}</td>
                    </tr>
                @endforeach
            </x-ui.data-table>
        @endif
    </x-ui.card>
</x-admin-layout>
