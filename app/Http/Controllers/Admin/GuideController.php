<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGuideRequest;
use App\Http\Requests\Admin\UpdateGuideRequest;
use App\Models\Guide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GuideController extends Controller
{
    public function index(Request $request): View
    {
        $guides = Guide::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->withCount('bookings')
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.guides.index', ['guides' => $guides]);
    }

    public function create(): View
    {
        return view('admin.guides.create');
    }

    public function store(StoreGuideRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? '') ?: Str::slug($data['name']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('guides', 'public');
        } else {
            unset($data['photo']);
        }

        Guide::create($data);

        return redirect()->route('admin.guides.index')->with('status', 'Guide created.');
    }

    public function edit(Guide $guide): View
    {
        return view('admin.guides.edit', ['guide' => $guide]);
    }

    public function update(UpdateGuideRequest $request, Guide $guide): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($guide->photo) {
                Storage::disk('public')->delete($guide->photo);
            }
            $data['photo'] = $request->file('photo')->store('guides', 'public');
        } else {
            unset($data['photo']);
        }

        $guide->update($data);

        return redirect()->route('admin.guides.index')->with('status', 'Guide updated.');
    }

    public function destroy(Guide $guide): RedirectResponse
    {
        if ($guide->photo) {
            Storage::disk('public')->delete($guide->photo);
        }

        $guide->delete();

        return redirect()->route('admin.guides.index')->with('status', 'Guide deleted.');
    }
}
