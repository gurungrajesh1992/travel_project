<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Destination;
use App\Models\Tour;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('website.home', [
            'featuredTours' => Tour::published()->where('is_featured', true)->withOrderedDestinations()->withOrderedCategories()->take(6)->get(),
            'destinations' => Destination::active()->topLevel()->orderBy('sort_order')->get(),
            'latestPosts' => BlogPost::published()->latest('published_at')->take(3)->get(),
        ]);
    }
}
