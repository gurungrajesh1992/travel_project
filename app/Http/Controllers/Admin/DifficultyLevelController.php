<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDifficultyLevelRequest;
use App\Http\Requests\Admin\UpdateDifficultyLevelRequest;
use App\Models\DifficultyLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DifficultyLevelController extends Controller
{
    public function index(Request $request): View
    {
        $difficultyLevels = DifficultyLevel::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->orderBy('sort_order')

            ->paginate(15)
            ->withQueryString();

        return view('admin.difficulty-levels.index', ['difficultyLevels' => $difficultyLevels]);
    }

    public function create(): View
    {
        return view('admin.difficulty-levels.create', [

        ]);
    }

    public function store(StoreDifficultyLevelRequest $request): RedirectResponse
    {
        DifficultyLevel::create($request->validated());

        return redirect()->route('admin.difficulty-levels.index')->with('status', 'Difficulty Level created.');
    }

    public function edit(DifficultyLevel $difficultyLevel): View
    {
        return view('admin.difficulty-levels.edit', [
            'difficultyLevel' => $difficultyLevel,

        ]);
    }

    public function update(UpdateDifficultyLevelRequest $request, DifficultyLevel $difficultyLevel): RedirectResponse
    {
        $difficultyLevel->update($request->validated());

        return redirect()->route('admin.difficulty-levels.index')->with('status', 'Difficulty Level updated.');
    }

    public function destroy(DifficultyLevel $difficultyLevel): RedirectResponse
    {
        $difficultyLevel->delete();

        return redirect()->route('admin.difficulty-levels.index')->with('status', 'Difficulty Level deleted.');
    }
}
