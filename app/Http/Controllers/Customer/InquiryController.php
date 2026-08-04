<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $inquiries = Inquiry::query()
            ->where('email', $request->user()->email)
            ->with('tour')
            ->latest('created_at')
            ->paginate(10);

        return view('customer.inquiries.index', compact('inquiries'));
    }
}
