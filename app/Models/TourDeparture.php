<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourDeparture extends Model
{
    public $timestamps = false;

    protected $fillable = ['tour_id', 'departure_date', 'return_date', 'available_seats', 'booked_seats', 'status'];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'departure_id');
    }

    public function remainingSeats(): int
    {
        return max(0, $this->available_seats - $this->booked_seats);
    }
}
