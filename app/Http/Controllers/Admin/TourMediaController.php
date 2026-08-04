<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TourMediaController extends Controller
{
    public function store(Request $request, Tour $tour): RedirectResponse
    {
        if ($request->hasFile('files')) {
            $data = $request->validate([
                'files' => ['required', 'array', 'min:1'],
                'files.*' => ['image', 'max:4096'],
            ]);

            $nextOrder = $tour->media()->count();

            foreach ($data['files'] as $i => $file) {
                $tour->media()->create([
                    'media_type' => 'image',
                    'file_path' => $file->store('tours/gallery', 'public'),
                    'sort_order' => $nextOrder + $i,
                ]);
            }

            return back()->with('status', count($data['files']).' photo(s) added.');
        }

        $data = $request->validate([
            'video_url' => ['required', 'url'],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $tour->media()->create([
            'media_type' => 'video',
            'video_url' => $data['video_url'],
            'caption' => $data['caption'] ?? null,
            'sort_order' => $tour->media()->count(),
        ]);

        return back()->with('status', 'Video added.');
    }

    public function destroy(Tour $tour, TourMedia $media): RedirectResponse
    {
        abort_unless($media->tour_id === $tour->id, 404);

        if ($media->file_path) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

        return back()->with('status', 'Gallery item removed.');
    }
}
