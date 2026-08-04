@php $money = fn ($v) => $currency.' '.number_format($v ?? 0, 2); @endphp

<x-admin-layout title="Popular Tours Report">
    <x-slot name="header">Popular Tours Report</x-slot>

    @vite(['resources/js/reports.js'])

    @include('admin.reports._tabs')
    @include('admin.reports._date-filter')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-ui.card title="Top 10 by Bookings" class="lg:col-span-1">
            @if (empty($chartData['data']['labels']))
                <p class="text-sm text-gray-500">No bookings in this period.</p>
            @else
                <canvas data-chart="{{ json_encode($chartData) }}" height="220"></canvas>
            @endif
        </x-ui.card>

        <x-ui.card title="Tour Performance" class="lg:col-span-2">
            @if ($tours->isEmpty())
                <x-ui.empty-state title="No bookings in this period" description="Try a wider date range." />
            @else
                <x-ui.data-table :headers="['Tour', 'Bookings', 'Revenue', 'Avg Rating']">
                    @foreach ($tours as $tour)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <a href="{{ route('admin.tours.edit', $tour) }}" class="hover:text-primary">{{ $tour->title }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $tour->bookings_count }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $money($tour->revenue_sum) }}</td>
                            <td class="px-4 py-3 text-yellow-600">{{ $tour->avg_rating ? number_format($tour->avg_rating, 1).' ★' : '—' }}</td>
                        </tr>
                    @endforeach
                </x-ui.data-table>
            @endif
        </x-ui.card>
    </div>
</x-admin-layout>
