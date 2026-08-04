<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourItineraryMedia extends Model
{
    public $timestamps = false;

    protected $fillable = ['tour_itinerary_id', 'file_path', 'caption', 'sort_order'];

    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(TourItinerary::class, 'tour_itinerary_id');
    }
}
