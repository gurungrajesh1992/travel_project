<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Tour extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('nav.destinations'));
        static::deleted(fn () => Cache::forget('nav.destinations'));
    }

    protected $fillable = [
        'vendor_id', 'primary_destination_id', 'primary_category_id', 'difficulty_id', 'guide_id',
        'title', 'slug', 'short_description', 'full_description',
        'duration_days', 'duration_nights', 'max_altitude', 'group_size_min', 'group_size_max', 'best_season',
        'base_price', 'currency', 'total_seats', 'thumbnail', 'map_type', 'map_data',
        'meta_title', 'meta_description', 'is_featured', 'booking_mode', 'status',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'base_price' => 'decimal:2',
            'map_data' => 'array',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function primaryDestination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'primary_destination_id');
    }

    public function primaryCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'primary_category_id');
    }

    public function difficulty(): BelongsTo
    {
        return $this->belongsTo(DifficultyLevel::class, 'difficulty_id');
    }

    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class, 'tour_destinations')->withPivot('is_primary');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'tour_categories')->withPivot('is_primary');
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(TourHighlight::class)->orderBy('sort_order');
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(TourItinerary::class)->orderBy('day_number');
    }

    public function costDetails(): HasMany
    {
        return $this->hasMany(TourCostDetail::class)->orderBy('sort_order');
    }

    public function includes(): HasMany
    {
        return $this->costDetails()->where('type', 'include');
    }

    public function excludes(): HasMany
    {
        return $this->costDetails()->where('type', 'exclude');
    }

    public function media(): HasMany
    {
        return $this->hasMany(TourMedia::class)->orderBy('sort_order');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(TourFaq::class)->orderBy('sort_order');
    }

    public function departures(): HasMany
    {
        return $this->hasMany(TourDeparture::class)->orderBy('departure_date');
    }

    public function seasonalPricing(): HasMany
    {
        return $this->hasMany(TourSeasonalPricing::class);
    }

    public function pricingTiers(): HasMany
    {
        return $this->hasMany(TourPricingTier::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true)->latest('created_at');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Eager-load destinations ordered primary-first, matching the ordering
     * used for the multi-country nav (see MultiCountryNavResolver).
     */
    public function scopeWithOrderedDestinations($query)
    {
        return $query->with(['destinations' => fn ($q) => $q
            ->orderByDesc('tour_destinations.is_primary')
            ->orderBy('destinations.sort_order')]);
    }

    /**
     * Eager-load categories ordered primary-first, with each category's
     * parent loaded so categoriesLabel() can roll subcategories up without
     * an N+1 query.
     */
    public function scopeWithOrderedCategories($query)
    {
        return $query->with(['categories' => fn ($q) => $q
            ->orderByDesc('tour_categories.is_primary')
            ->orderBy('categories.sort_order')
            ->with('parent:id,name')]);
    }

    /**
     * "Nepal" for a single-destination tour, "Nepal-India-Bhutan" (primary
     * first) for a multi-destination one.
     */
    public function destinationsLabel(): string
    {
        $destinations = $this->relationLoaded('destinations') ? $this->destinations : $this->destinations()->get();

        if ($destinations->count() <= 1) {
            return $destinations->first()?->name ?? (string) $this->primaryDestination?->name;
        }

        return $destinations->pluck('name')->implode('-');
    }

    /**
     * "Trekking" for a single-category tour. For a multi-category tour,
     * subcategories roll up to their parent first (a tour tagged with both
     * "Everest Trek" and "Annapurna Trek" — both children of "Trekking" —
     * should only show "Trekking" once), then joins as
     * "Trekking-Peak Climbing-Expedition" (primary first).
     */
    public function categoriesLabel(): string
    {
        $categories = $this->relationLoaded('categories') ? $this->categories : $this->categories()->with('parent:id,name')->get();

        if ($categories->count() <= 1) {
            return $categories->first()?->name ?? (string) $this->primaryCategory?->name;
        }

        return $categories
            ->map(fn (Category $category) => $category->parent_id ? $category->parent?->name : $category->name)
            ->filter()
            ->unique()
            ->implode('-');
    }
}
