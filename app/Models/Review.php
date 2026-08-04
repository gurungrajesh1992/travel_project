<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tour_id', 'booking_id', 'user_id', 'reviewer_name', 'reviewer_country',
        'rating', 'review_text', 'is_approved', 'created_at',
    ];

    protected function casts(): array
    {
        return ['is_approved' => 'boolean', 'created_at' => 'datetime'];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
