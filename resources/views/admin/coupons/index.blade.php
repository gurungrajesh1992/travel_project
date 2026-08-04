<x-admin-layout title="Coupons">
    <x-slot name="header">Coupons</x-slot>

    <x-ui.card>
        <x-slot name="actions">
            <x-ui.button as="a" href="{{ route('admin.coupons.create') }}" size="sm">Add Coupon</x-ui.button>
        </x-slot>

        <form method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code..."
                   class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm text-sm focus:border-primary focus:ring-primary">
        </form>

        @if ($coupons->isEmpty())
            <x-ui.empty-state title="No Coupons yet" description="Click &quot;Add Coupon&quot; to create the first one." />
        @else
            <x-ui.data-table :headers="['Code', 'Discount', 'Valid', 'Usage', 'Status', '']">
                @foreach ($coupons as $coupon)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $coupon->code }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $coupon->type === 'percentage' ? $coupon->value.'%' : number_format($coupon->value, 2) }}
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $coupon->valid_from?->format('M j, Y') ?? '—' }} – {{ $coupon->valid_until?->format('M j, Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $coupon->used_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}
                        </td>
                        <td class="px-4 py-3"><x-ui.badge :color="$coupon->status ? 'green' : 'gray'">{{ $coupon->status ? 'Active' : 'Inactive' }}</x-ui.badge></td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('Delete this Coupon?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$coupons" />
        @endif
    </x-ui.card>
</x-admin-layout>
