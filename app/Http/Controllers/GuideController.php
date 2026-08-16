<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use Illuminate\View\View;

class GuideController extends Controller
{
    public function index(): View
    {
        $guides = Guide::active()->latest()->get();

        return view('website.guides.index', compact('guides'));
    }

    public function show(Guide $guide): View
    {
        abort_unless($guide->status, 404);

        $guide->load(['tours' => fn ($q) => $q->published()->withOrderedDestinations()->withOrderedCategories()]);

        return view('website.guides.show', compact('guide'));
    }
}
