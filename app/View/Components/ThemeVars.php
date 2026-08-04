<?php

namespace App\View\Components;

use App\Services\ThemeService;
use Illuminate\View\Component;
use Illuminate\View\View;

class ThemeVars extends Component
{
    public string $css;

    public function __construct(string $panel)
    {
        $this->css = app(ThemeService::class)->cssVariables($panel);
    }

    public function render(): View
    {
        return view('components.theme-vars');
    }
}
