<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guide extends Model
{
    protected $fillable = [
        'name', 'slug', 'photo', 'bio', 'languages', 'experience_years',
        'phone', 'email', 'status', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
