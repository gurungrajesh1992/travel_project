<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ThemeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThemeSettingsController extends Controller
{
    public function edit(ThemeService $theme): View
    {
        $panels = collect(config('theme.panels'))
            ->mapWithKeys(fn ($panel) => [$panel => $theme->colors($panel)]);

        return view('admin.settings.theme', compact('panels'));
    }

    public function update(Request $request, ThemeService $theme): RedirectResponse
    {
        $panels = config('theme.panels');
        $keys = collect(array_keys(config('theme.defaults.'.$panels[0])))
            ->reject(fn ($key) => str_ends_with($key, '_content'))
            ->all();

        $data = $request->validate(
            collect($panels)
                ->crossJoin($keys)
                ->mapWithKeys(fn ($pair) => ["{$pair[0]}.{$pair[1]}" => ['required', 'string', 'max:20']])
                ->all()
        );

        foreach ($data as $panel => $colors) {
            foreach ($colors as $key => $value) {
                $theme->set($panel, $key, $value);
            }
        }

        return back()->with('status', 'Theme colors updated.');
    }
}
