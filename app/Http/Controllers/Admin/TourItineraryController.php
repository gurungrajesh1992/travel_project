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
            'area' => ['nullable', 'array'],
            'area.*' => ['nullable', 'string', 'max:255'],
            'detail_itinerary' => ['nullable', 'array'],
            'detail_itinerary.*' => ['nullable', 'string'],
            'transportation' => ['nullable', 'array'],
            'transportation.*' => ['nullable', 'string', 'max:100'],
            'time' => ['nullable', 'array'],
            'time.*' => ['nullable', 'string', 'max:20'],
        ]);

        $added = 0;

        foreach ($data['day_number'] as $i => $dayNumber) {
            $area = $data['area'][$i] ?? null;

            if (empty($dayNumber) || trim((string) $area) === '') {
                continue;
            }

            $tour->itineraries()->create([
                'day_number' => $dayNumber,
                'area' => $area,
                'detail_itinerary' => $data['detail_itinerary'][$i] ?? null,
                'transportation' => $data['transportation'][$i] ?? null,
                'time' => $data['time'][$i] ?? null,
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
