<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $wishlists = $request->user()->wishlists()
            ->with('tour')
            ->latest('created_at')
            ->paginate(12);

        return view('customer.wishlist.index', compact('wishlists'));
    }

    public function destroy(Request $request, Tour $tour): RedirectResponse
    {
        $request->user()->wishlists()->where('tour_id', $tour->id)->delete();

        return back()->with('status', 'Removed from wishlist.');
    }
}
