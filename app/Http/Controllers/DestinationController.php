<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Tour;
use App\Services\CategoryNavResolver;
use App\Services\MultiCountryNavResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function show(Request $request, Destination $destination): View
    {
        $tours = Tour::published()
            ->whereHas('destinations', fn ($q) => $q->where('destinations.id', $destination->id))
            ->when($request->filled('category'), function ($q) use ($request) {
                $category = Category::where('slug', $request->string('category'))->first();

                if ($category) {
                    $q->whereHas('categories', fn ($c) => $c->whereIn('categories.id', CategoryNavResolver::idsForBrowsing($category)));
                }
            })
            ->withOrderedDestinations()
            ->withOrderedCategories()
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = $this->usedCategories($destination);

        return view('website.destinations.show', compact('destination', 'tours', 'categories'));
    }

    public function category(Request $request, Destination $destination, Category $category): View
    {
        $categoryIds = CategoryNavResolver::idsForBrowsing($category);

        $tours = Tour::published()
            ->whereHas('destinations', fn ($q) => $q->where('destinations.id', $destination->id))
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->withOrderedDestinations()
            ->withOrderedCategories()
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = $this->usedCategories($destination);

        return view('website.destinations.category', compact('destination', 'category', 'tours', 'categories'));
    }

    private function usedCategories(Destination $destination): \Illuminate\Support\Collection
    {
        $toursInDestination = Tour::published()
            ->whereHas('destinations', fn ($q) => $q->where('destinations.id', $destination->id))
            ->with('categories:id')
            ->get();

        $usedCategoryIds = $toursInDestination->flatMap(fn ($tour) => $tour->categories->pluck('id'))->unique();

        return CategoryNavResolver::resolve($usedCategoryIds);
    }

    public function multiCountry(Request $request): View
    {
        $selectedSlugs = collect(explode(',', (string) $request->string('destinations')))
            ->map(fn ($slug) => trim($slug))
            ->filter()
            ->all();

        $tours = Tour::published()
            ->has('destinations', '>', 1)
            ->when(! empty($selectedSlugs), function ($q) use ($selectedSlugs) {
                foreach ($selectedSlugs as $slug) {
                    $q->whereHas('destinations', fn ($d) => $d->where('destinations.slug', $slug));
                }
            })
            ->withOrderedDestinations()
            ->withOrderedCategories()
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $combos = MultiCountryNavResolver::combos();

        return view('website.destinations.multi-country', compact('tours', 'combos', 'selectedSlugs'));
    }
}
