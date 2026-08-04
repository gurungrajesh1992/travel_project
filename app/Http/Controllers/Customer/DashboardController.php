<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('customer.dashboard', [
            'bookingCount' => $user->bookings()->count(),
            'upcomingBooking' => $user->bookings()
                ->whereIn('booking_status', ['pending', 'confirmed'])
                ->with('tour')
                ->latest()
                ->first(),
            'wishlistCount' => $user->wishlists()->count(),
        ]);
    }
}
