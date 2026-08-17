<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourItinerary extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tour_id', 'destination_id', 'day_number', 'area', 'detail_itinerary',
        'altitude', 'meals', 'transportation', 'time', 'distance_km',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(TourItineraryMedia::class)->orderBy('sort_order');
    }
}
