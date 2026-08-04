@php $money = fn ($v) => $currency.' '.number_format($v, 2); @endphp

<x-admin-layout title="Bookings Report">
    <x-slot name="header">Monthly / Yearly Bookings</x-slot>

    @vite(['resources/js/reports.js'])

    @include('admin.reports._tabs')

    <form method="GET" class="flex flex-wrap items-end gap-3 mb-6">
        <div class="flex gap-1.5">
            <a href="{{ route('admin.reports.bookings', ['period' => 'monthly']) }}"
               class="px-3 py-1.5 text-xs rounded-md border {{ $period === 'monthly' ? 'bg-primary text-primary-content border-primary' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">Monthly</a>
            <a href="{{ route('admin.reports.bookings', ['period' => 'yearly']) }}"
               class="px-3 py-1.5 text-xs rounded-md border {{ $period === 'yearly' ? 'bg-primary text-primary-content border-primary' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">Yearly</a>
        </div>

        @if ($period === 'monthly')
            <div class="sm:ml-auto">
                <input type="hidden" name="period" value="monthly">
                <select name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
                    @foreach (range($currentYear, $currentYear - 4) as $y)
                        <option value="{{ $y }}" @selected($year === $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </form>

    <x-ui.card :title="$period === 'monthly' ? 'Bookings for '.$year : 'Bookings by Year'">
        <canvas data-chart="{{ json_encode($chartData) }}" height="90"></canvas>
    </x-ui.card>

    <x-ui.card class="mt-6">
        <x-ui.data-table :headers="[$period === 'monthly' ? 'Month' : 'Year', 'Bookings', 'Cancelled', 'Revenue']">
            @foreach ($rows as $row)
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $row->label }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $row->total }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $row->cancelled }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $money($row->revenue) }}</td>
                </tr>
            @endforeach
        </x-ui.data-table>
    </x-ui.card>
</x-admin-layout>
