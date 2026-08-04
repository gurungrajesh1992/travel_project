<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'code', 'type', 'value', 'min_booking_amount', 'max_discount_amount',
        'usage_limit', 'used_count', 'valid_from', 'valid_until', 'status',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_until' => 'date',
            'status' => 'boolean',
        ];
    }

    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class, 'coupon_tours');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_categories');
    }

    public function isValidNow(): bool
    {
        $today = now()->toDateString();

        if (! $this->status) {
            return false;
        }

        if ($this->valid_from && $today < $this->valid_from->toDateString()) {
            return false;
        }

        if ($this->valid_until && $today > $this->valid_until->toDateString()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }
}
