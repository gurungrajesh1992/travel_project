<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourPricingTier extends Model
{
    public $timestamps = false;

    protected $fillable = ['tour_id', 'tier_type', 'min_pax', 'max_pax', 'price_per_person'];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
