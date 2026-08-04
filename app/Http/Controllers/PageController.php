<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::published()->where('slug', $slug)->firstOrFail();

        return view('website.pages.show', compact('page'));
    }
}
