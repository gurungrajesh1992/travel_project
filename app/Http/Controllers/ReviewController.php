<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Tour $tour): RedirectResponse
    {
        $data = $request->validate([
            'reviewer_name' => ['required', 'string', 'max:150'],
            'reviewer_country' => ['nullable', 'string', 'max:100'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['required', 'string', 'max:2000'],
        ]);

        $tour->reviews()->create($data + [
            'user_id' => $request->user()?->id,
            'created_at' => now(),
        ]);

        return back()->with('status', 'Thanks for your review! It will appear once approved.');
    }
}
