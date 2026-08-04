<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'gallery_category_id', 'tour_id', 'media_type', 'file_path',
        'video_url', 'caption', 'is_featured', 'sort_order', 'created_at',
    ];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(GalleryCategory::class, 'gallery_category_id');
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
