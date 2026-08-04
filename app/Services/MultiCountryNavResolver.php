<?php

namespace App\Services;

use App\Models\Tour;
use Illuminate\Support\Collection;

/**
 * Builds the list of distinct destination combinations covered by
 * multi-country tours (e.g. "Nepal-India", "Bhutan-Tibet"), primary
 * destination first per tour, for the "Multi-Country" nav dropdown and
 * the multi-country listing page's filter pills.
 */
class MultiCountryNavResolver
{
    /**
     * @return Collection<int, array{label: string, slugs: string}>
     */
    public static function combos(): Collection
    {
        return Tour::published()
            ->has('destinations', '>', 1)
            ->with(['destinations' => fn ($q) => $q->orderByDesc('tour_destinations.is_primary')->orderBy('destinations.sort_order')])
            ->get()
            ->map(function (Tour $tour) {
                $ordered = $tour->destinations;

                return [
                    'label' => $ordered->pluck('name')->implode('-'),
                    'slugs' => $ordered->pluck('slug')->implode(','),
                ];
            })
            ->unique('slugs')
            ->sortBy('label')
            ->values();
    }
}
