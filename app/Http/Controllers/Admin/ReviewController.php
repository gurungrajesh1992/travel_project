<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = Review::query()
            ->when($request->filled('search'), fn ($q) => $q->where('reviewer_name', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('approved'), fn ($q) => $q->where('is_approved', $request->string('approved') === '1'))
            ->when($request->filled('rating'), fn ($q) => $q->where('rating', $request->integer('rating')))
            ->with('tour')
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reviews.index', ['reviews' => $reviews]);
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => true]);

        return back()->with('status', 'Review approved and now visible on the site.');
    }

    public function unapprove(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => false]);

        return back()->with('status', 'Review hidden from the site.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('status', 'Review deleted.');
    }
}
