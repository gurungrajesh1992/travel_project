<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\BlogPost;
use App\Models\Destination;
use App\Models\Tour;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('website.home', [
            'banners' => Banner::active()->orderBy('sort_order')->get(),
            'featuredTours' => Tour::published()->where('is_featured', true)->withOrderedDestinations()->withOrderedCategories()->latest()->take(6)->get(),
            'destinations' => Destination::active()->topLevel()->orderBy('sort_order')->get(),
            'latestPosts' => BlogPost::published()->latest('id')->take(3)->get(),
        ]);
    }
}
