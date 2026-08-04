<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $categories = FaqCategory::orderBy('sort_order')
            ->with(['faqs' => fn ($q) => $q->active()])
            ->get();

        return view('website.faq.index', compact('categories'));
    }
}
