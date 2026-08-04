<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = $request->user()->bookings()
            ->with('tour')
            ->latest()
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function show(Request $request, Booking $booking): View
    {
        abort_unless($booking->user_id === $request->user()->id, 403);

        $booking->load(['tour', 'travelers', 'payments', 'statusLogs', 'departure', 'coupon']);

        return view('customer.bookings.show', compact('booking'));
    }
}
