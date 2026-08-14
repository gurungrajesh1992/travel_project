<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        if ($request->hasFile('files')) {
            $data = $request->validate([
                'files' => ['required', 'array', 'min:1'],
                'files.*' => ['image', 'max:4096'],
            ]);

            $nextOrder = Banner::count();

            foreach ($data['files'] as $i => $file) {
                Banner::create([
                    'media_type' => 'image',
                    'file_path' => $file->store('banners', 'public'),
                    'sort_order' => $nextOrder + $i,
                ]);
            }

            return back()->with('status', count($data['files']).' banner image(s) added.');
        }

        $data = $request->validate([
            'video_url' => ['required', 'url'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        Banner::create([
            'media_type' => 'video',
            'video_url' => $data['video_url'],
            'title' => $data['title'] ?? null,
            'sort_order' => Banner::count(),
        ]);

        return back()->with('status', 'Banner video added.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        if ($banner->file_path) {
            Storage::disk('public')->delete($banner->file_path);
        }

        $banner->delete();

        return back()->with('status', 'Banner removed.');
    }
}
