<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'booking_ref', 'tour_id', 'departure_id', 'vendor_id', 'user_id', 'coupon_id', 'guide_id',
        'booking_type', 'guest_name', 'guest_email', 'guest_phone',
        'num_adults', 'num_children', 'pricing_tier_id',
        'subtotal', 'discount_amount', 'total_amount', 'deposit_required',
        'booking_status', 'payment_status', 'cancellation_reason', 'cancelled_at',
        'special_requests', 'source',
    ];

    protected function casts(): array
    {
        return ['cancelled_at' => 'datetime'];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function departure(): BelongsTo
    {
        return $this->belongsTo(TourDeparture::class, 'departure_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }

    public function pricingTier(): BelongsTo
    {
        return $this->belongsTo(TourPricingTier::class, 'pricing_tier_id');
    }

    public function travelers(): HasMany
    {
        return $this->hasMany(BookingTraveler::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(BookingStatusLog::class)->latest();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function review(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function leadTraveler(): ?BookingTraveler
    {
        return $this->travelers->firstWhere('is_lead_traveler', true);
    }

    public function customerName(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Guest';
    }
}
