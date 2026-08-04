<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourItinerary;
use App\Models\TourItineraryMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TourItineraryController extends Controller
{
    public function store(Request $request, Tour $tour): RedirectResponse
    {
        $data = $request->validate([
            'day_number' => ['required', 'array', 'min:1'],
            'day_number.*' => ['nullable', 'integer', 'min:1'],
            'title' => ['nullable', 'array'],
            'title.*' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string'],
            'accommodation' => ['nullable', 'array'],
            'accommodation.*' => ['nullable', 'string', 'max:100'],
            'walking_hours' => ['nullable', 'array'],
            'walking_hours.*' => ['nullable', 'string', 'max:20'],
        ]);

        $added = 0;

        foreach ($data['day_number'] as $i => $dayNumber) {
            $title = $data['title'][$i] ?? null;

            if (empty($dayNumber) || trim((string) $title) === '') {
                continue;
            }

            $tour->itineraries()->create([
                'day_number' => $dayNumber,
                'title' => $title,
                'description' => $data['description'][$i] ?? null,
                'accommodation' => $data['accommodation'][$i] ?? null,
                'walking_hours' => $data['walking_hours'][$i] ?? null,
            ]);
            $added++;
        }

        return back()->with('status', $added > 0 ? "{$added} itinerary day(s) added." : 'Nothing to add.');
    }

    public function destroy(Tour $tour, TourItinerary $itinerary): RedirectResponse
    {
        abort_unless($itinerary->tour_id === $tour->id, 404);
        $itinerary->delete();

        return back()->with('status', 'Itinerary day removed.');
    }

    public function storeMedia(Request $request, Tour $tour, TourItinerary $itinerary): RedirectResponse
    {
        abort_unless($itinerary->tour_id === $tour->id, 404);

        $data = $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['image', 'max:4096'],
        ]);

        $nextOrder = $itinerary->media()->count();

        foreach ($data['files'] as $i => $file) {
            $itinerary->media()->create([
                'file_path' => $file->store('tours/itineraries', 'public'),
                'sort_order' => $nextOrder + $i,
            ]);
        }

        return back()->with('status', count($data['files']).' photo(s) added.');
    }

    public function destroyMedia(Tour $tour, TourItinerary $itinerary, TourItineraryMedia $media): RedirectResponse
    {
        abort_unless($itinerary->tour_id === $tour->id && $media->tour_itinerary_id === $itinerary->id, 404);

        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return back()->with('status', 'Photo removed.');
    }
}
