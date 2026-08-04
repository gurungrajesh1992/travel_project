<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::query()
            ->whereHas('booking', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with('booking.tour')
            ->latest('created_at')
            ->paginate(10);

        return view('customer.payments.index', compact('payments'));
    }
}
