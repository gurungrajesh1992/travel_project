<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGalleryItemRequest;
use App\Http\Requests\Admin\UpdateGalleryItemRequest;
use App\Models\GalleryCategory;
use App\Models\GalleryItem;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryItemController extends Controller
{
    public function index(Request $request): View
    {
        $galleryItems = GalleryItem::query()
            ->when($request->filled('search'), fn ($q) => $q->where('caption', 'like', '%'.$request->string('search').'%'))
            ->with('category')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.gallery-items.index', ['galleryItems' => $galleryItems]);
    }

    public function create(): View
    {
        return view('admin.gallery-items.create', $this->formOptions());
    }

    public function store(StoreGalleryItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_featured'] = $request->boolean('is_featured');
        $data['created_at'] = now();

        if ($data['media_type'] === 'image' && $request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('gallery', 'public');
            $data['video_url'] = null;
        } else {
            $data['file_path'] = null;
        }
        unset($data['file']);

        GalleryItem::create($data);

        return redirect()->route('admin.gallery-items.index')->with('status', 'Gallery item created.');
    }

    public function edit(GalleryItem $galleryItem): View
    {
        return view('admin.gallery-items.edit', ['galleryItem' => $galleryItem] + $this->formOptions());
    }

    public function update(UpdateGalleryItemRequest $request, GalleryItem $galleryItem): RedirectResponse
    {
        $data = $request->validated();
        $data['is_featured'] = $request->boolean('is_featured');

        if ($data['media_type'] === 'image') {
            if ($request->hasFile('file')) {
                if ($galleryItem->file_path) {
                    Storage::disk('public')->delete($galleryItem->file_path);
                }
                $data['file_path'] = $request->file('file')->store('gallery', 'public');
            } else {
                $data['file_path'] = $galleryItem->file_path;
            }
            $data['video_url'] = null;
        } else {
            if ($galleryItem->file_path) {
                Storage::disk('public')->delete($galleryItem->file_path);
            }
            $data['file_path'] = null;
        }
        unset($data['file']);

        $galleryItem->update($data);

        return redirect()->route('admin.gallery-items.index')->with('status', 'Gallery item updated.');
    }

    public function destroy(GalleryItem $galleryItem): RedirectResponse
    {
        if ($galleryItem->file_path) {
            Storage::disk('public')->delete($galleryItem->file_path);
        }

        $galleryItem->delete();

        return redirect()->route('admin.gallery-items.index')->with('status', 'Gallery item deleted.');
    }

    private function formOptions(): array
    {
        return [
            'categoryOptions' => GalleryCategory::orderBy('sort_order')->pluck('name', 'id'),
            'tourOptions' => Tour::orderBy('title')->pluck('title', 'id'),
        ];
    }
}
