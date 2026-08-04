@php
    $statusColors = ['pending' => 'yellow', 'confirmed' => 'primary', 'completed' => 'green', 'cancelled' => 'red'];
    $paymentColors = ['unpaid' => 'gray', 'partial' => 'yellow', 'paid' => 'green', 'refunded' => 'red'];
    $paymentRowColors = ['pending' => 'yellow', 'success' => 'green', 'failed' => 'red', 'refunded' => 'red'];
@endphp

<x-admin-layout title="Booking {{ $booking->booking_ref }}">
    <x-slot name="header">Booking {{ $booking->booking_ref }}</x-slot>

    <div class="space-y-6">
        <x-ui.card title="Booking Summary" collapsible>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 text-sm">
                <dt class="text-gray-500">Tour</dt>
                <dd class="text-gray-900">{{ $booking->tour->title }}</dd>

                <dt class="text-gray-500">Departure</dt>
                <dd class="text-gray-900">{{ $booking->departure?->departure_date->format('M j, Y') ?? 'Flexible' }}</dd>

                <dt class="text-gray-500">Customer</dt>
                <dd class="text-gray-900">
                    {{ $booking->customerName() }}
                    <span class="text-gray-400">({{ $booking->user ? 'registered' : 'guest' }})</span>
                    @if ($booking->guest_email || $booking->user?->email)
                        <br><span class="text-gray-500">{{ $booking->guest_email ?? $booking->user->email }}</span>
                    @endif
                    @if ($booking->guest_phone)
                        <br><span class="text-gray-500">{{ $booking->guest_phone }}</span>
                    @endif
                </dd>

                <dt class="text-gray-500">Travelers</dt>
                <dd class="text-gray-900">{{ $booking->num_adults }} adult(s), {{ $booking->num_children }} child(ren)</dd>

                <dt class="text-gray-500">Booking Type</dt>
                <dd class="text-gray-900">{{ ucfirst($booking->booking_type) }} &middot; via {{ ucfirst($booking->source) }}</dd>

                <dt class="text-gray-500">Submitted</dt>
                <dd class="text-gray-900">{{ $booking->created_at->format('M j, Y g:i A') }}</dd>

                @if ($booking->special_requests)
                    <dt class="text-gray-500">Special Requests</dt>
                    <dd class="text-gray-900">{{ $booking->special_requests }}</dd>
                @endif
            </dl>
        </x-ui.card>

        <x-ui.card title="Pricing" collapsible>
            <dl class="grid grid-cols-2 gap-y-2 text-sm max-w-md">
                <dt class="text-gray-500">Subtotal</dt>
                <dd class="text-gray-900">{{ $booking->tour->currency }} {{ number_format($booking->subtotal, 2) }}</dd>

                @if ($booking->discount_amount > 0)
                    <dt class="text-gray-500">Discount {{ $booking->coupon ? '('.$booking->coupon->code.')' : '' }}</dt>
                    <dd class="text-green-600">&minus; {{ $booking->tour->currency }} {{ number_format($booking->discount_amount, 2) }}</dd>
                @endif

                <dt class="text-gray-500 font-medium">Total</dt>
                <dd class="text-gray-900 font-semibold">{{ $booking->tour->currency }} {{ number_format($booking->total_amount, 2) }}</dd>

                @if ($booking->deposit_required)
                    <dt class="text-gray-500">Deposit Required</dt>
                    <dd class="text-gray-900">{{ $booking->tour->currency }} {{ number_format($booking->deposit_required, 2) }}</dd>
                @endif
            </dl>
        </x-ui.card>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-ui.card title="Booking Status" collapsible>
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-sm text-gray-500">Current:</span>
                    <x-ui.badge :color="$statusColors[$booking->booking_status] ?? 'gray'">{{ ucfirst($booking->booking_status) }}</x-ui.badge>
                    <span class="text-sm text-gray-500 ml-2">Payment:</span>
                    <x-ui.badge :color="$paymentColors[$booking->payment_status] ?? 'gray'">{{ ucfirst($booking->payment_status) }}</x-ui.badge>
                </div>

                <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" x-data="{ status: '{{ $booking->booking_status }}' }" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <x-ui.select label="Change Status" name="booking_status" x-model="status"
                                 :options="['pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled']"
                                 :selected="$booking->booking_status" />

                    <div x-show="status === 'cancelled'" x-cloak>
                        <x-ui.textarea label="Cancellation Reason" name="cancellation_reason" :value="$booking->cancellation_reason ?? ''" rows="2" />
                    </div>

                    <div x-show="status !== 'cancelled'" x-cloak>
                        <x-ui.textarea label="Note (optional)" name="note" rows="2" />
                    </div>

                    <x-ui.button type="submit" size="sm">Update Status</x-ui.button>
                </form>
            </x-ui.card>

            <x-ui.card title="Guide Assignment" collapsible>
                <form method="POST" action="{{ route('admin.bookings.guide', $booking) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <x-ui.select label="Assigned Guide" name="guide_id" :options="$guides->pluck('name', 'id')" :selected="$booking->guide_id" placeholder="None" />

                    <x-ui.button type="submit" size="sm">Save Guide</x-ui.button>
                </form>
            </x-ui.card>
        </div>

        <x-ui.card title="Payments" subtitle="Verify uploaded receipts and mark payments as success/failed." collapsible collapsed>
            @if ($booking->payments->isEmpty())
                <p class="text-sm text-gray-500">No payments submitted yet.</p>
            @else
                <x-ui.data-table :headers="['Date', 'Method', 'Amount', 'Receipt', 'Status', '']">
                    @foreach ($booking->payments as $payment)
                        <tr>
                            <td class="px-4 py-3 text-gray-500">{{ $payment->created_at?->format('M j, Y g:i A') }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $payment->payment_method }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $booking->tour->currency }} {{ number_format($payment->amount, 2) }}</td>
                            <td class="px-4 py-3">
                                @if ($payment->receipt_path)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($payment->receipt_path) }}" target="_blank" class="text-primary hover:underline">View</a>
                                @else
                                    <span class="text-gray-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-4 py-3"><x-ui.badge :color="$paymentRowColors[$payment->status] ?? 'gray'">{{ ucfirst($payment->status) }}</x-ui.badge></td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.bookings.payments.update', $payment) }}" class="inline-flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="rounded-md border-gray-300 shadow-sm text-xs focus:border-primary focus:ring-primary">
                                        @foreach (['pending', 'success', 'failed', 'refunded'] as $status)
                                            <option value="{{ $status }}" @selected($payment->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="text-primary hover:underline text-xs font-medium">Save</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.data-table>
            @endif
        </x-ui.card>

        <x-ui.card title="Status History" collapsible collapsed>
            @if ($booking->statusLogs->isEmpty())
                <p class="text-sm text-gray-500">No status changes recorded yet.</p>
            @else
                <div class="space-y-3">
                    @foreach ($booking->statusLogs as $log)
                        <div class="text-sm border-b border-gray-100 pb-2">
                            <span class="text-gray-900 font-medium">{{ $log->from_status ? ucfirst($log->from_status).' → ' : '' }}{{ ucfirst($log->to_status) }}</span>
                            <span class="text-gray-400">by {{ $log->changedBy?->name ?? 'System' }} &middot; {{ $log->created_at?->format('M j, Y g:i A') }}</span>
                            @if ($log->note)
                                <p class="text-gray-500 mt-0.5">{{ $log->note }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>

        <div>
            <x-ui.button as="a" href="{{ route('admin.bookings.index') }}" variant="secondary">Back to Bookings</x-ui.button>
        </div>
    </div>
</x-admin-layout>
