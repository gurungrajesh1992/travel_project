@php $money = fn ($v) => $currency.' '.number_format($v ?? 0, 2); @endphp

<x-admin-layout title="Customers">
    <x-slot name="header">Customers</x-slot>

    <x-ui.card>
        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($customers->isEmpty())
            <x-ui.empty-state title="No customers yet" description="Registered customers will show up here." />
        @else
            <x-ui.data-table :headers="['Customer', 'Bookings', 'Total Spent', 'Joined', 'Status', '']">
                @foreach ($customers as $customer)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $customer->name }}</p>
                            <p class="text-xs text-gray-500">{{ $customer->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $customer->bookings_count }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $money($customer->total_spent) }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $customer->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3"><x-ui.badge :color="$customer->isSuspended() ? 'red' : 'green'">{{ $customer->isSuspended() ? 'Suspended' : 'Active' }}</x-ui.badge></td>
                        <td class="px-4 py-3 text-right space-x-3 whitespace-nowrap">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-primary hover:underline">View</a>
                            @if ($customer->isSuspended())
                                <form method="POST" action="{{ route('admin.customers.activate', $customer) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-600 hover:underline">Activate</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.customers.suspend', $customer) }}" class="inline" onsubmit="return confirm('Suspend this customer? They will not be able to log in.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-red-600 hover:underline">Suspend</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$customers" />
        @endif
    </x-ui.card>
</x-admin-layout>
