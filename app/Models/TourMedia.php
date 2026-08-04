<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourMedia extends Model
{
    public $timestamps = false;

    protected $fillable = ['tour_id', 'media_type', 'file_path', 'video_url', 'caption', 'sort_order'];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
