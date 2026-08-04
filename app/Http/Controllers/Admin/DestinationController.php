<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDestinationRequest;
use App\Http\Requests\Admin\UpdateDestinationRequest;
use App\Models\Destination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(Request $request): View
    {
        $destinations = Destination::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->orderBy('sort_order')

            ->paginate(15)
            ->withQueryString();

        return view('admin.destinations.index', ['destinations' => $destinations]);
    }

    public function create(): View
    {
        return view('admin.destinations.create', [
            'parentOptions' => \App\Models\Destination::whereNull('parent_id')->orderBy('name')->pluck('name', 'id'),

        ]);
    }

    public function store(StoreDestinationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ($data['slug'] ?? '') ?: \Illuminate\Support\Str::slug($data['name']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('destinations', 'public');
        } else {
            unset($data['thumbnail']);
        }

        Destination::create($data);

        return redirect()->route('admin.destinations.index')->with('status', 'Destination created.');
    }

    public function edit(Destination $destination): View
    {
        return view('admin.destinations.edit', [
            'destination' => $destination,
            'parentOptions' => \App\Models\Destination::whereNull('parent_id')->where('id', '!=', $destination->id)->orderBy('name')->pluck('name', 'id'),

        ]);
    }

    public function update(UpdateDestinationRequest $request, Destination $destination): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            if ($destination->thumbnail) {
                Storage::disk('public')->delete($destination->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('destinations', 'public');
        } else {
            unset($data['thumbnail']);
        }

        $destination->update($data);

        return redirect()->route('admin.destinations.index')->with('status', 'Destination updated.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        if ($destination->thumbnail) {
            Storage::disk('public')->delete($destination->thumbnail);
        }

        $destination->delete();

        return redirect()->route('admin.destinations.index')->with('status', 'Destination deleted.');
    }
}
