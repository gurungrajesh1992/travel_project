<x-customer-layout title="Payments">
    <x-slot name="header">Payment History</x-slot>

    <x-ui.card>
        @if ($payments->isEmpty())
            <x-ui.empty-state title="No payments yet" description="Payments you make toward a booking will appear here." />
        @else
            <x-ui.data-table :headers="['Date', 'Booking', 'Method', 'Amount', 'Status']">
                @foreach ($payments as $payment)
                    <tr>
                        <td class="px-4 py-3">{{ $payment->created_at?->format('M j, Y') }}</td>
                        <td class="px-4 py-3">{{ $payment->booking->booking_ref }} &mdash; {{ $payment->booking->tour->title }}</td>
                        <td class="px-4 py-3">{{ $payment->payment_method }}</td>
                        <td class="px-4 py-3">{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-4 py-3"><x-ui.badge :color="$payment->status === 'success' ? 'green' : ($payment->status === 'failed' ? 'red' : 'gray')">{{ ucfirst($payment->status) }}</x-ui.badge></td>
                    </tr>
                @endforeach
            </x-ui.data-table>

            <x-ui.pagination-links :paginator="$payments" />
        @endif
    </x-ui.card>
</x-customer-layout>
