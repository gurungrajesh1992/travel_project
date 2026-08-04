<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingTraveler extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'booking_id', 'full_name', 'passport_number', 'nationality',
        'date_of_birth', 'gender', 'is_lead_traveler',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_lead_traveler' => 'boolean',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
