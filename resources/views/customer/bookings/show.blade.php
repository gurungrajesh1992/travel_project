<x-customer-layout title="Booking {{ $booking->booking_ref }}">
    <x-slot name="header">Booking {{ $booking->booking_ref }}</x-slot>

    <div class="space-y-6">
        <x-ui.card title="Tour">
            <p class="font-medium text-gray-900">{{ $booking->tour->title }}</p>
            @if ($booking->departure)
                <p class="text-sm text-gray-500 mt-1">
                    Departure: {{ $booking->departure->departure_date->format('M j, Y') }}
                </p>
            @endif
        </x-ui.card>

        <x-ui.card title="Pricing">
            <dl class="grid grid-cols-2 gap-y-2 text-sm">
                <dt class="text-gray-500">Subtotal</dt>
                <dd class="text-gray-900">{{ $booking->tour->currency }} {{ number_format($booking->subtotal, 2) }}</dd>

                @if ($booking->discount_amount > 0)
                    <dt class="text-gray-500">Discount {{ $booking->coupon ? '('.$booking->coupon->code.')' : '' }}</dt>
                    <dd class="text-green-600">&minus; {{ $booking->tour->currency }} {{ number_format($booking->discount_amount, 2) }}</dd>
                @endif

                <dt class="text-gray-500">Total</dt>
                <dd class="text-gray-900 font-semibold">{{ $booking->tour->currency }} {{ number_format($booking->total_amount, 2) }}</dd>
            </dl>
        </x-ui.card>

        <x-ui.card title="Status">
            <div class="flex gap-3">
                <x-ui.badge :color="match($booking->booking_status) { 'confirmed' => 'green', 'cancelled' => 'red', 'completed' => 'blue', default => 'gray' }">{{ ucfirst($booking->booking_status) }}</x-ui.badge>
                <x-ui.badge :color="match($booking->payment_status) { 'paid' => 'green', 'partial' => 'yellow', 'refunded' => 'red', default => 'gray' }">{{ ucfirst($booking->payment_status) }}</x-ui.badge>
            </div>

            @if ($booking->cancellation_reason)
                <p class="mt-3 text-sm text-gray-600">Cancellation reason: {{ $booking->cancellation_reason }}</p>
            @endif
        </x-ui.card>

        <x-ui.card title="Travelers">
            @forelse ($booking->travelers as $traveler)
                <p class="text-sm text-gray-700">{{ $traveler->full_name }} @if($traveler->is_lead_traveler) <span class="text-xs text-primary">(Lead)</span> @endif</p>
            @empty
                <p class="text-sm text-gray-500">No traveler details recorded.</p>
            @endforelse
        </x-ui.card>

        <x-ui.card title="Payments">
            @forelse ($booking->payments as $payment)
                <div class="flex justify-between text-sm py-1">
                    <span>{{ $payment->payment_method }} &mdash; {{ $payment->created_at?->format('M j, Y') }}</span>
                    <span class="font-medium">{{ number_format($payment->amount, 2) }} ({{ $payment->status }})</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">No payments recorded yet.</p>
            @endforelse
        </x-ui.card>
    </div>
</x-customer-layout>
