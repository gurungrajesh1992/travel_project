<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourFaq extends Model
{
    public $timestamps = false;

    protected $fillable = ['tour_id', 'question', 'answer', 'sort_order'];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
