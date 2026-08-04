<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Tour;
use App\Models\TourPricingTier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function store(Request $request, Tour $tour): RedirectResponse
    {
        $data = $request->validate([
            'departure_id' => ['nullable', 'exists:tour_departures,id'],
            'pricing_tier_id' => ['nullable', 'exists:tour_pricing_tiers,id'],
            'guide_id' => ['nullable', 'exists:guides,id'],
            'num_adults' => ['required', 'integer', 'min:1'],
            'num_children' => ['nullable', 'integer', 'min:0'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'booking_type' => ['required', 'in:instant,inquiry'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
            'guest_name' => ['required_if:booking_type,instant', 'nullable', 'string', 'max:150'],
            'guest_email' => ['required_if:booking_type,instant', 'nullable', 'email', 'max:150'],
            'guest_phone' => ['nullable', 'string', 'max:30'],
        ]);

        $numAdults = $data['num_adults'];
        $numChildren = $data['num_children'] ?? 0;

        [$subtotal, $pricingTierId] = $this->calculateSubtotal($tour, $data['pricing_tier_id'] ?? null, $numAdults, $numChildren);

        $discount = 0;
        $coupon = null;
        $couponError = null;

        if (! empty($data['coupon_code'])) {
            $coupon = Coupon::where('code', strtoupper($data['coupon_code']))->first();

            if ($coupon && $coupon->isValidNow() && $this->couponAppliesToTour($coupon, $tour) && $subtotal >= (float) ($coupon->min_booking_amount ?? 0)) {
                $discount = $coupon->type === 'percentage'
                    ? $subtotal * ($coupon->value / 100)
                    : $coupon->value;

                if ($coupon->max_discount_amount) {
                    $discount = min($discount, (float) $coupon->max_discount_amount);
                }
            } else {
                $couponError = 'That coupon code is invalid or not applicable to this booking. Your booking was submitted without a discount.';
                $coupon = null;
            }
        }

        $booking = DB::transaction(function () use ($request, $tour, $data, $numAdults, $numChildren, $pricingTierId, $subtotal, $discount, $coupon) {
            $booking = Booking::create([
                'booking_ref' => 'TT-'.strtoupper(Str::random(8)),
                'tour_id' => $tour->id,
                'departure_id' => $data['departure_id'] ?? null,
                'user_id' => $request->user()?->id,
                'coupon_id' => $coupon?->id,
                'guide_id' => $data['guide_id'] ?? null,
                'booking_type' => $data['booking_type'],
                'guest_name' => $request->user() ? null : ($data['guest_name'] ?? null),
                'guest_email' => $request->user() ? null : ($data['guest_email'] ?? null),
                'guest_phone' => $data['guest_phone'] ?? null,
                'num_adults' => $numAdults,
                'num_children' => $numChildren,
                'pricing_tier_id' => $pricingTierId,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => max(0, $subtotal - $discount),
                'booking_status' => 'pending',
                'payment_status' => 'unpaid',
                'special_requests' => $data['special_requests'] ?? null,
                'source' => 'website',
            ]);

            $booking->statusLogs()->create([
                'to_status' => 'pending',
                'changed_by' => $request->user()?->id,
                'note' => 'Booking submitted from website.',
                'created_at' => now(),
            ]);

            if ($coupon) {
                $coupon->increment('used_count');
            }

            if ($data['departure_id'] ?? null) {
                $tour->departures()->where('id', $data['departure_id'])->increment('booked_seats', $numAdults + $numChildren);
            }

            return $booking;
        });

        $redirect = redirect()->route('bookings.confirmation', $booking)
            ->with('status', 'Booking submitted! Reference: '.$booking->booking_ref);

        if ($couponError) {
            $redirect->with('couponError', $couponError);
        }

        return $redirect;
    }

    public function confirmation(Booking $booking): View
    {
        $booking->load(['tour', 'departure', 'payments', 'coupon']);

        return view('website.bookings.confirmation', compact('booking'));
    }

    public function uploadPayment(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'max:50'],
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $booking->payments()->create([
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'receipt_path' => $request->file('receipt')->store('payment-receipts', 'public'),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        if ($booking->payment_status === 'unpaid') {
            $booking->update(['payment_status' => 'partial']);
        }

        return back()->with('status', 'Payment receipt uploaded. Our team will verify it shortly.');
    }

    /**
     * @return array{0: float, 1: int|null}
     */
    private function calculateSubtotal(Tour $tour, ?int $pricingTierId, int $numAdults, int $numChildren): array
    {
        $adultTier = $pricingTierId ? TourPricingTier::find($pricingTierId) : null;
        $childTier = TourPricingTier::where('tour_id', $tour->id)->where('tier_type', 'child')->first();

        $adultPrice = $adultTier?->price_per_person ?? $tour->base_price;
        $childPrice = $childTier?->price_per_person ?? $tour->base_price;

        $subtotal = ($adultPrice * $numAdults) + ($childPrice * $numChildren);

        return [(float) $subtotal, $adultTier?->id];
    }

    private function couponAppliesToTour(Coupon $coupon, Tour $tour): bool
    {
        if ($coupon->tours()->exists() && $coupon->tours()->where('tours.id', $tour->id)->exists()) {
            return true;
        }

        if ($coupon->categories()->exists()) {
            $tourCategoryIds = $tour->categories()->pluck('categories.id');

            return $coupon->categories()->whereIn('categories.id', $tourCategoryIds)->exists();
        }

        return ! $coupon->tours()->exists() && ! $coupon->categories()->exists();
    }
}
