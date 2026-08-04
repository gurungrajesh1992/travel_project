<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class ThemeService
{
    /**
     * Get the resolved colors for a panel, falling back to config/theme.php
     * defaults for any key not overridden in the `settings` table.
     */
    public function colors(string $panel): array
    {
        $defaults = config("theme.defaults.{$panel}");

        if ($defaults === null) {
            throw new InvalidArgumentException("Unknown theme panel [{$panel}].");
        }

        return Cache::rememberForever("theme.{$panel}", function () use ($panel, $defaults) {
            $colors = [];

            foreach ($defaults as $key => $default) {
                $colors[$key] = Setting::get("theme.{$panel}.{$key}", $default);
            }

            return $colors;
        });
    }

    /**
     * Persist an override for one panel/key and bust its cache.
     */
    public function set(string $panel, string $key, string $value): void
    {
        Setting::set("theme.{$panel}.{$key}", $value);
        Cache::forget("theme.{$panel}");
    }

    /**
     * Render the CSS custom properties for a panel as a `:root { ... }` block.
     */
    public function cssVariables(string $panel): string
    {
        $vars = [];

        foreach ($this->colors($panel) as $key => $value) {
            $vars[] = '--tt-'.str_replace('_', '-', $key).': '.$value.';';
        }

        return ':root { '.implode(' ', $vars).' }';
    }
}
