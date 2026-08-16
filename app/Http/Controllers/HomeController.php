<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Review;
use App\Models\Tour;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $trekParent = Category::active()->where('slug', 'trekking')->first();

        return view('website.home', [
            'banners' => Banner::active()->orderBy('sort_order')->get(),
            'featuredTours' => Tour::published()->where('is_featured', true)->withOrderedDestinations()->withOrderedCategories()->latest()->take(6)->get(),
            'trekCategories' => $trekParent
                ? Category::active()
                    ->where('parent_id', $trekParent->id)
                    ->withCount(['tours' => fn ($q) => $q->published()])
                    ->orderByDesc('tours_count')
                    ->get()
                : collect(),
            'latestReviews' => Review::with(['tour:id,title', 'user:id,avatar'])
                ->where('is_approved', true)
                ->latest('created_at')
                ->take(12)
                ->get(),
            'latestPosts' => BlogPost::published()->latest('id')->take(3)->get(),
        ]);
    }
}
