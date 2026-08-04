<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Inquiry;
use App\Models\Tour;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'tourCount' => Tour::count(),
            'publishedTourCount' => Tour::published()->count(),
            'bookingCount' => Booking::count(),
            'pendingBookingCount' => Booking::where('booking_status', 'pending')->count(),
            'customerCount' => User::where('role', 'customer')->count(),
            'openInquiryCount' => Inquiry::where('status', 'new')->count(),
            'recentBookings' => Booking::with('tour')->latest()->take(5)->get(),
        ]);
    }
}
