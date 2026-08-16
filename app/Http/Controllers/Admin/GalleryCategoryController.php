<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGalleryCategoryRequest;
use App\Http\Requests\Admin\UpdateGalleryCategoryRequest;
use App\Models\GalleryCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GalleryCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $galleryCategories = GalleryCategory::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->withCount('items')
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.gallery-categories.index', ['galleryCategories' => $galleryCategories]);
    }

    public function create(): View
    {
        return view('admin.gallery-categories.create');
    }

    public function store(StoreGalleryCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['name']);

        GalleryCategory::create($data);

        return redirect()->route('admin.gallery-categories.index')->with('status', 'Gallery category created.');
    }

    public function edit(GalleryCategory $galleryCategory): View
    {
        return view('admin.gallery-categories.edit', ['galleryCategory' => $galleryCategory]);
    }

    public function update(UpdateGalleryCategoryRequest $request, GalleryCategory $galleryCategory): RedirectResponse
    {
        $galleryCategory->update($request->validated());

        return redirect()->route('admin.gallery-categories.index')->with('status', 'Gallery category updated.');
    }

    public function destroy(GalleryCategory $galleryCategory): RedirectResponse
    {
        $galleryCategory->delete();

        return redirect()->route('admin.gallery-categories.index')->with('status', 'Gallery category deleted.');
    }
}
