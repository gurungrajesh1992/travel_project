<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;

/**
 * Rolls a flat set of category IDs (which may include subcategories) up into
 * top-level categories, each carrying only the subcategories that were
 * actually used. A tour tagged with a subcategory (e.g. "Everest Trek", a
 * child of "Trekking") must still surface "Trekking" in destination
 * nav/listings — matching by top-level category ID alone misses that.
 */
class CategoryNavResolver
{
    /**
     * @param  Collection<int, int>  $usedCategoryIds
     * @return Collection<int, array{id: int, name: string, slug: string, children: array}>
     */
    public static function resolve(Collection $usedCategoryIds): Collection
    {
        $allCategories = Category::active()->orderBy('sort_order')->get(['id', 'name', 'slug', 'parent_id']);

        $topLevelIds = $allCategories
            ->whereIn('id', $usedCategoryIds)
            ->map(fn (Category $category) => $category->parent_id ?? $category->id)
            ->unique();

        return $allCategories
            ->whereNull('parent_id')
            ->whereIn('id', $topLevelIds)
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'children' => $allCategories
                    ->where('parent_id', $category->id)
                    ->whereIn('id', $usedCategoryIds)
                    ->map(fn (Category $child) => ['id' => $child->id, 'name' => $child->name, 'slug' => $child->slug])
                    ->values()
                    ->all(),
            ])
            ->values();
    }

    /**
     * All category IDs that should match "browsing category $category" —
     * itself alone if it's already a subcategory, or itself plus every
     * child if it's a top-level category (so a parent listing includes
     * tours tagged only with one of its subcategories).
     *
     * @return array<int, int>
     */
    public static function idsForBrowsing(Category $category): array
    {
        if ($category->parent_id) {
            return [$category->id];
        }

        return Category::where('id', $category->id)
            ->orWhere('parent_id', $category->id)
            ->pluck('id')
            ->all();
    }
}
