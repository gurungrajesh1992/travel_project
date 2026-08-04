<?php

namespace App\Http\Controllers;

use App\Models\GalleryCategory;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $categories = GalleryCategory::where('status', true)
            ->orderBy('sort_order')
            ->with('items')
            ->get();

        return view('website.gallery.index', compact('categories'));
    }
}
