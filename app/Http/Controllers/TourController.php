<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourController extends Controller
{
    public function index(Request $request): View
    {
        $tours = Tour::published()
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('destination'), fn ($q) => $q->whereHas(
                'destinations',
                fn ($d) => $d->where('destinations.slug', $request->string('destination'))
            ))
            ->when($request->filled('category'), fn ($q) => $q->whereHas(
                'categories',
                fn ($c) => $c->where('categories.slug', $request->string('category'))
            ))
            ->withOrderedDestinations()
            ->withOrderedCategories()
            ->with('difficulty')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('website.tours.index', [
            'tours' => $tours,
            'destinations' => Destination::active()->topLevel()->orderBy('name')->get(),
            'categories' => Category::active()->topLevel()->orderBy('name')->get(),
        ]);
    }

    public function show(Tour $tour): View
    {
        abort_unless($tour->status === 'published', 404);

        $tour->load([
            'primaryDestination',
            'destinations' => fn ($q) => $q->orderByDesc('tour_destinations.is_primary')->orderBy('destinations.sort_order'),
            'categories' => fn ($q) => $q->orderByDesc('tour_categories.is_primary')->orderBy('categories.sort_order')->with('parent:id,name'),
            'difficulty', 'guide',
            'highlights', 'itineraries.media', 'itineraries.destination',
            'costDetails', 'media', 'faqs', 'departures' => fn ($q) => $q->where('departure_date', '>=', now())->where('status', 'open'),
            'seasonalPricing', 'pricingTiers',
            'approvedReviews',
        ]);

        $relatedTours = Tour::published()
            ->where('id', '!=', $tour->id)
            ->where('primary_destination_id', $tour->primary_destination_id)
            ->withOrderedDestinations()
            ->withOrderedCategories()
            ->take(3)
            ->get();

        $avgRating = $tour->approvedReviews->avg('rating');

        return view('website.tours.show', compact('tour', 'relatedTours', 'avgRating'));
    }
}
