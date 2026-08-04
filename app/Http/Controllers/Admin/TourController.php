<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourRequest;
use App\Http\Requests\Admin\UpdateTourRequest;
use App\Models\Category;
use App\Models\Destination;
use App\Models\DifficultyLevel;
use App\Models\Guide;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TourController extends Controller
{
    public function index(Request $request): View
    {
        $tours = Tour::query()
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('destination'), fn ($q) => $q->where('primary_destination_id', $request->integer('destination')))
            ->when($request->filled('category'), fn ($q) => $q->where('primary_category_id', $request->integer('category')))
            ->with(['primaryDestination', 'primaryCategory', 'difficulty'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.tours.index', [
            'tours' => $tours,
            'destinations' => Destination::active()->orderBy('name')->get(),
            'categories' => Category::active()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.tours.create', $this->formOptions());
    }

    public function store(StoreTourRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['title']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['map_data'] = isset($data['map_data']) ? json_decode($data['map_data'], true) : null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('tours', 'public');
        }

        $tour = Tour::create($data);

        $this->syncPivots($tour, $request);

        return redirect()->route('admin.tours.edit', $tour)->with('status', 'Tour created. Add itinerary, pricing, and media below.');
    }

    public function edit(Tour $tour): View
    {
        $tour->load([
            'destinations', 'categories', 'highlights', 'itineraries.media', 'itineraries.destination',
            'costDetails', 'media', 'faqs', 'departures', 'seasonalPricing', 'pricingTiers',
        ]);

        return view('admin.tours.edit', ['tour' => $tour] + $this->formOptions());
    }

    public function update(UpdateTourRequest $request, Tour $tour): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['title']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['map_data'] = isset($data['map_data']) ? json_decode($data['map_data'], true) : null;

        if ($request->hasFile('thumbnail')) {
            if ($tour->thumbnail) {
                Storage::disk('public')->delete($tour->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('tours', 'public');
        }

        $tour->update($data);

        $this->syncPivots($tour, $request);

        return redirect()->route('admin.tours.edit', $tour)->with('status', 'Tour updated.');
    }

    public function destroy(Tour $tour): RedirectResponse
    {
        $tour->delete();

        return redirect()->route('admin.tours.index')->with('status', 'Tour deleted.');
    }

    private function syncPivots(Tour $tour, Request $request): void
    {
        $destinations = $request->input('destinations', []);
        $categories = $request->input('categories', []);

        $primaryDestination = $request->input('primary_destination');
        if (! in_array($primaryDestination, $destinations)) {
            $primaryDestination = $destinations[0] ?? null;
        }

        $primaryCategory = $request->input('primary_category');
        if (! in_array($primaryCategory, $categories)) {
            $primaryCategory = $categories[0] ?? null;
        }

        $tour->destinations()->sync(collect($destinations)->mapWithKeys(
            fn ($id) => [$id => ['is_primary' => (string) $id === (string) $primaryDestination]]
        ));

        $tour->categories()->sync(collect($categories)->mapWithKeys(
            fn ($id) => [$id => ['is_primary' => (string) $id === (string) $primaryCategory]]
        ));

        $tour->update([
            'primary_destination_id' => $primaryDestination,
            'primary_category_id' => $primaryCategory,
        ]);
    }

    private function formOptions(): array
    {
        return [
            'destinations' => Destination::active()->orderBy('name')->get(),
            'categories' => Category::active()->orderBy('parent_id')->orderBy('name')->get(),
            'difficulties' => DifficultyLevel::orderBy('sort_order')->get(),
            'guides' => Guide::active()->orderBy('name')->get(),
        ];
    }
}
