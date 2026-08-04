<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function store(Request $request, ?Tour $tour = null): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        Inquiry::create($data + [
            'tour_id' => $tour?->id,
            'created_at' => now(),
        ]);

        return back()->with('status', 'Thanks for reaching out — we will get back to you shortly.');
    }
}
