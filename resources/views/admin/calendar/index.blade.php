@php
    $statusColors = ['open' => 'green', 'full' => 'yellow', 'cancelled' => 'red'];
@endphp

<x-admin-layout title="Calendar">
    <x-slot name="header">Departure Calendar</x-slot>

    @vite(['resources/js/calendar.js'])

    <div class="space-y-6">
        <x-ui.card>
            <div id="departure-calendar" data-events-url="{{ route('admin.calendar.events') }}"></div>

            <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-green-500"></span> Open</span>
                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-yellow-500"></span> Full</span>
                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-red-500"></span> Cancelled</span>
            </div>
        </x-ui.card>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-ui.card title="Currently Running" subtitle="Departures underway right now." collapsible>
                @if ($runningDepartures->isEmpty())
                    <p class="text-sm text-gray-500">No tours are currently running.</p>
                @else
                    <x-ui.data-table :headers="['Tour', 'Departed', 'Returns', 'Seats Left', 'Status']">
                        @foreach ($runningDepartures as $departure)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    <a href="{{ route('admin.tours.edit', $departure->tour) }}" class="hover:text-primary">{{ $departure->tour->title }}</a>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $departure->departure_date->format('M j, Y') }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $departure->return_date?->format('M j, Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $departure->remainingSeats() }}</td>
                                <td class="px-4 py-3"><x-ui.badge :color="$statusColors[$departure->status] ?? 'gray'">{{ ucfirst($departure->status) }}</x-ui.badge></td>
                            </tr>
                        @endforeach
                    </x-ui.data-table>
                @endif
            </x-ui.card>

            <x-ui.card title="Upcoming Departures" subtitle="Next scheduled departures." collapsible>
                @if ($upcomingDepartures->isEmpty())
                    <p class="text-sm text-gray-500">No upcoming departures scheduled.</p>
                @else
                    <x-ui.data-table :headers="['Tour', 'Departs', 'Returns', 'Seats Left', 'Status']">
                        @foreach ($upcomingDepartures as $departure)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    <a href="{{ route('admin.tours.edit', $departure->tour) }}" class="hover:text-primary">{{ $departure->tour->title }}</a>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $departure->departure_date->format('M j, Y') }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $departure->return_date?->format('M j, Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $departure->remainingSeats() }}</td>
                                <td class="px-4 py-3"><x-ui.badge :color="$statusColors[$departure->status] ?? 'gray'">{{ ucfirst($departure->status) }}</x-ui.badge></td>
                            </tr>
                        @endforeach
                    </x-ui.data-table>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-admin-layout>
