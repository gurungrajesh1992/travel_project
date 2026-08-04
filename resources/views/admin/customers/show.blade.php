@php
    $money = fn ($v) => $currency.' '.number_format($v ?? 0, 2);
    $statusColors = ['pending' => 'yellow', 'confirmed' => 'primary', 'completed' => 'green', 'cancelled' => 'red'];
    $paymentColors = ['unpaid' => 'gray', 'partial' => 'yellow', 'paid' => 'green', 'refunded' => 'red'];
@endphp

<x-admin-layout title="{{ $customer->name }}">
    <x-slot name="header">{{ $customer->name }}</x-slot>

    <div class="space-y-6">
        <x-ui.card title="Profile" collapsible>
            <div class="flex items-center justify-between">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 text-sm">
                    <dt class="text-gray-500">Email</dt>
                    <dd class="text-gray-900">{{ $customer->email }}</dd>

                    @if ($customer->phone)
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="text-gray-900">{{ $customer->phone }}</dd>
                    @endif

                    @if ($customer->country)
                        <dt class="text-gray-500">Country</dt>
                        <dd class="text-gray-900">{{ $customer->country }}</dd>
                    @endif

                    <dt class="text-gray-500">Joined</dt>
                    <dd class="text-gray-900">{{ $customer->created_at->format('M j, Y') }}</dd>

                    <dt class="text-gray-500">Status</dt>
                    <dd><x-ui.badge :color="$customer->isSuspended() ? 'red' : 'green'">{{ $customer->isSuspended() ? 'Suspended' : 'Active' }}</x-ui.badge></dd>

                    @if ($customer->isSuspended())
                        <dt class="text-gray-500">Suspended On</dt>
                        <dd class="text-gray-900">{{ $customer->suspended_at->format('M j, Y g:i A') }}</dd>
                    @endif
                </dl>

                <div>
                    @if ($customer->isSuspended())
                        <form method="POST" action="{{ route('admin.customers.activate', $customer) }}">
                            @csrf
                            @method('PATCH')
                            <x-ui.button type="submit" size="sm">Reactivate Account</x-ui.button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.customers.suspend', $customer) }}" onsubmit="return confirm('Suspend this customer? They will not be able to log in.')">
                            @csrf
                            @method('PATCH')
                            <x-ui.button type="submit" variant="danger" size="sm">Suspend Account</x-ui.button>
                        </form>
                    @endif
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Booking History" collapsible>
            @if ($customer->bookings->isEmpty())
                <p class="text-sm text-gray-500">No bookings yet.</p>
            @else
                <x-ui.data-table :headers="['Ref', 'Tour', 'Total', 'Status', 'Payment', 'Booked']">
                    @foreach ($customer->bookings as $booking)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="hover:text-primary">{{ $booking->booking_ref }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $booking->tour->title }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $money($booking->total_amount) }}</td>
                            <td class="px-4 py-3"><x-ui.badge :color="$statusColors[$booking->booking_status] ?? 'gray'">{{ ucfirst($booking->booking_status) }}</x-ui.badge></td>
                            <td class="px-4 py-3"><x-ui.badge :color="$paymentColors[$booking->payment_status] ?? 'gray'">{{ ucfirst($booking->payment_status) }}</x-ui.badge></td>
                            <td class="px-4 py-3 text-gray-500">{{ $booking->created_at->format('M j, Y') }}</td>
                        </tr>
                    @endforeach
                </x-ui.data-table>
            @endif
        </x-ui.card>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-ui.card title="Reviews" collapsible collapsed>
                @if ($customer->reviews->isEmpty())
                    <p class="text-sm text-gray-500">No reviews submitted.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($customer->reviews as $review)
                            <div class="border-b border-gray-100 pb-2 text-sm">
                                <p class="font-medium text-gray-900">{{ $review->tour?->title ?? '—' }} <span class="text-yellow-600">{{ str_repeat('★', $review->rating) }}</span></p>
                                <p class="text-gray-500">{{ $review->review_text }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            <x-ui.card title="Wishlist" collapsible collapsed>
                @if ($customer->wishlists->isEmpty())
                    <p class="text-sm text-gray-500">No tours wishlisted.</p>
                @else
                    <ul class="text-sm text-gray-700 space-y-2">
                        @foreach ($customer->wishlists as $wishlist)
                            <li>{{ $wishlist->tour?->title ?? '—' }} <span class="text-gray-400">&middot; added {{ $wishlist->created_at?->format('M j, Y') }}</span></li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        </div>

        <div>
            <x-ui.button as="a" href="{{ route('admin.customers.index') }}" variant="secondary">Back to Customers</x-ui.button>
        </div>
    </div>
</x-admin-layout>
