<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourCostDetail extends Model
{
    public $timestamps = false;

    protected $fillable = ['tour_id', 'type', 'detail_text', 'sort_order'];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
