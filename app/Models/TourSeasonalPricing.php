<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourSeasonalPricing extends Model
{
    public $timestamps = false;

    protected $table = 'tour_seasonal_pricing';

    protected $fillable = ['tour_id', 'season_name', 'start_date', 'end_date', 'price'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
