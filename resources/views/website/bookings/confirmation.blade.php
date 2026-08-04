<x-website-layout title="Booking Confirmation">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
        <x-ui.alert type="success" dismissible :auto-dismiss="5">
            Booking submitted! Your reference is <strong>{{ $booking->booking_ref }}</strong>. Save this to check your booking status.
        </x-ui.alert>

        @if (session('couponError'))
            <x-ui.alert type="warning" dismissible>{{ session('couponError') }}</x-ui.alert>
        @endif

        <x-ui.card title="Booking Summary">
            <dl class="grid grid-cols-2 gap-y-3 text-sm">
                <dt class="text-gray-500">Tour</dt>
                <dd class="text-gray-900">{{ $booking->tour->title }}</dd>

                <dt class="text-gray-500">Departure</dt>
                <dd class="text-gray-900">{{ $booking->departure?->departure_date->format('M j, Y') ?? 'Flexible' }}</dd>

                <dt class="text-gray-500">Travelers</dt>
                <dd class="text-gray-900">{{ $booking->num_adults }} adult(s), {{ $booking->num_children }} child(ren)</dd>

                <dt class="text-gray-500">Subtotal</dt>
                <dd class="text-gray-900">{{ $booking->tour->currency }} {{ number_format($booking->subtotal, 2) }}</dd>

                @if ($booking->discount_amount > 0)
                    <dt class="text-gray-500">Discount {{ $booking->coupon ? '('.$booking->coupon->code.')' : '' }}</dt>
                    <dd class="text-green-600">&minus; {{ $booking->tour->currency }} {{ number_format($booking->discount_amount, 2) }}</dd>
                @endif

                <dt class="text-gray-500">Total</dt>
                <dd class="text-gray-900 font-semibold">{{ $booking->tour->currency }} {{ number_format($booking->total_amount, 2) }}</dd>

                <dt class="text-gray-500">Status</dt>
                <dd><x-ui.badge color="primary">{{ ucfirst($booking->booking_status) }}</x-ui.badge></dd>

                <dt class="text-gray-500">Payment</dt>
                <dd><x-ui.badge :color="$booking->payment_status === 'paid' ? 'green' : 'yellow'">{{ ucfirst($booking->payment_status) }}</x-ui.badge></dd>
            </dl>
        </x-ui.card>

        <x-ui.card title="Upload Payment Receipt" subtitle="Pay via bank transfer and upload your receipt for verification.">
            <form method="POST" action="{{ route('bookings.payment', $booking) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-ui.input label="Amount Paid" name="amount" type="number" step="0.01" required />
                    <x-ui.input label="Payment Method" name="payment_method" placeholder="Bank transfer, eSewa, ..." required />
                </div>
                <div>
                    <x-input-label for="receipt" value="Receipt (image or PDF)" />
                    <input type="file" name="receipt" id="receipt" accept="image/*,.pdf" required class="mt-1 block w-full text-sm">
                    <x-input-error :messages="$errors->get('receipt')" class="mt-1" />
                </div>
                <x-ui.button type="submit">Upload Receipt</x-ui.button>
            </form>

            @if ($booking->payments->isNotEmpty())
                <div class="mt-6 border-t border-gray-100 pt-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Payments submitted</p>
                    @foreach ($booking->payments as $payment)
                        <div class="flex justify-between text-sm py-1">
                            <span>{{ $payment->payment_method }} &mdash; {{ number_format($payment->amount, 2) }}</span>
                            <x-ui.badge :color="$payment->status === 'success' ? 'green' : 'yellow'">{{ ucfirst($payment->status) }}</x-ui.badge>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>
</x-website-layout>
