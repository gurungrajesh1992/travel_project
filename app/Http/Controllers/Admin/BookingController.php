<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guide;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($q) use ($search) {
                    $q->where('booking_ref', 'like', "%{$search}%")
                        ->orWhere('guest_name', 'like', "%{$search}%")
                        ->orWhere('guest_email', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                        ->orWhereHas('tour', fn ($q) => $q->where('title', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('booking_status'), fn ($q) => $q->where('booking_status', $request->string('booking_status')))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->string('payment_status')))
            ->with(['tour', 'user'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', ['bookings' => $bookings]);
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'tour', 'departure', 'user', 'coupon', 'guide', 'vendor', 'pricingTier',
            'travelers', 'payments', 'statusLogs.changedBy',
        ]);

        return view('admin.bookings.show', [
            'booking' => $booking,
            'guides' => Guide::active()->orderBy('name')->get(),
        ]);
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'booking_status' => ['required', 'in:pending,confirmed,cancelled,completed'],
            'note' => ['nullable', 'string', 'max:255'],
            'cancellation_reason' => ['required_if:booking_status,cancelled', 'nullable', 'string', 'max:500'],
        ]);

        $fromStatus = $booking->booking_status;
        $toStatus = $data['booking_status'];

        if ($fromStatus === $toStatus) {
            return back()->with('status', 'Booking is already '.$toStatus.'.');
        }

        DB::transaction(function () use ($request, $booking, $data, $fromStatus, $toStatus) {
            $seats = $booking->num_adults + $booking->num_children;

            if ($toStatus === 'cancelled' && $fromStatus !== 'cancelled' && $booking->departure_id) {
                $booking->departure()->decrement('booked_seats', $seats);
            } elseif ($fromStatus === 'cancelled' && $toStatus !== 'cancelled' && $booking->departure_id) {
                $booking->departure()->increment('booked_seats', $seats);
            }

            $booking->update([
                'booking_status' => $toStatus,
                'cancellation_reason' => $toStatus === 'cancelled' ? $data['cancellation_reason'] : null,
                'cancelled_at' => $toStatus === 'cancelled' ? now() : null,
            ]);

            $booking->statusLogs()->create([
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'changed_by' => $request->user()->id,
                'note' => $data['note'] ?? $data['cancellation_reason'] ?? null,
                'created_at' => now(),
            ]);
        });

        return back()->with('status', 'Booking marked as '.$toStatus.'.');
    }

    public function assignGuide(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate(['guide_id' => ['nullable', 'exists:guides,id']]);

        $booking->update(['guide_id' => $data['guide_id'] ?? null]);

        return back()->with('status', 'Guide updated.');
    }

    public function updatePayment(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:pending,success,failed,refunded']]);

        $payment->update([
            'status' => $data['status'],
            'paid_at' => $data['status'] === 'success' ? ($payment->paid_at ?? now()) : $payment->paid_at,
        ]);

        $this->recomputePaymentStatus($payment->booking);

        return back()->with('status', 'Payment updated.');
    }

    private function recomputePaymentStatus(Booking $booking): void
    {
        $paid = $booking->payments()->where('status', 'success')->sum('amount');

        $status = match (true) {
            $paid <= 0 && $booking->payments()->where('status', 'refunded')->exists() => 'refunded',
            $paid >= (float) $booking->total_amount && $booking->total_amount > 0 => 'paid',
            $paid > 0 => 'partial',
            default => 'unpaid',
        };

        $booking->update(['payment_status' => $status]);
    }
}
