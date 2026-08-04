<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourDeparture;
use App\Models\TourPricingTier;
use App\Models\TourSeasonalPricing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TourPricingController extends Controller
{
    public function storeDeparture(Request $request, Tour $tour): RedirectResponse
    {
        $data = $request->validate([
            'departure_date' => ['required', 'array', 'min:1'],
            'departure_date.*' => ['nullable', 'date'],
            'return_date' => ['nullable', 'array'],
            'return_date.*' => ['nullable', 'date'],
            'available_seats' => ['nullable', 'array'],
            'available_seats.*' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'array'],
            'status.*' => ['nullable', 'in:open,full,cancelled'],
        ]);

        $added = 0;

        foreach ($data['departure_date'] as $i => $departureDate) {
            if (empty($departureDate)) {
                continue;
            }

            $returnDate = $data['return_date'][$i] ?? null;
            if ($returnDate && $returnDate < $departureDate) {
                continue;
            }

            $tour->departures()->create([
                'departure_date' => $departureDate,
                'return_date' => $returnDate,
                'available_seats' => $data['available_seats'][$i] ?? 0,
                'status' => $data['status'][$i] ?? 'open',
            ]);
            $added++;
        }

        return back()->with('status', $added > 0 ? "{$added} departure(s) added." : 'Nothing to add.');
    }

    public function destroyDeparture(Tour $tour, TourDeparture $departure): RedirectResponse
    {
        abort_unless($departure->tour_id === $tour->id, 404);
        $departure->delete();

        return back()->with('status', 'Departure removed.');
    }

    public function storeSeasonalPrice(Request $request, Tour $tour): RedirectResponse
    {
        $data = $request->validate([
            'season_name' => ['required', 'array', 'min:1'],
            'season_name.*' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'array'],
            'start_date.*' => ['nullable', 'date'],
            'end_date' => ['nullable', 'array'],
            'end_date.*' => ['nullable', 'date'],
            'price' => ['nullable', 'array'],
            'price.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $added = 0;

        foreach ($data['season_name'] as $i => $name) {
            $startDate = $data['start_date'][$i] ?? null;
            $endDate = $data['end_date'][$i] ?? null;
            $price = $data['price'][$i] ?? null;

            if (trim((string) $name) === '' || empty($startDate) || empty($endDate) || $price === null || $price === '') {
                continue;
            }

            if ($endDate < $startDate) {
                continue;
            }

            $tour->seasonalPricing()->create([
                'season_name' => $name,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'price' => $price,
            ]);
            $added++;
        }

        return back()->with('status', $added > 0 ? "{$added} seasonal price(s) added." : 'Nothing to add.');
    }

    public function destroySeasonalPrice(Tour $tour, TourSeasonalPricing $seasonalPricing): RedirectResponse
    {
        abort_unless($seasonalPricing->tour_id === $tour->id, 404);
        $seasonalPricing->delete();

        return back()->with('status', 'Seasonal price removed.');
    }

    public function storePricingTier(Request $request, Tour $tour): RedirectResponse
    {
        $data = $request->validate([
            'tier_type' => ['required', 'array', 'min:1'],
            'tier_type.*' => ['nullable', 'in:group,child,private,solo'],
            'min_pax' => ['nullable', 'array'],
            'min_pax.*' => ['nullable', 'integer', 'min:1'],
            'max_pax' => ['nullable', 'array'],
            'max_pax.*' => ['nullable', 'integer', 'min:1'],
            'price_per_person' => ['nullable', 'array'],
            'price_per_person.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $added = 0;

        foreach ($data['tier_type'] as $i => $type) {
            $price = $data['price_per_person'][$i] ?? null;

            if (empty($type) || $price === null || $price === '') {
                continue;
            }

            $tour->pricingTiers()->create([
                'tier_type' => $type,
                'min_pax' => $data['min_pax'][$i] ?? null,
                'max_pax' => $data['max_pax'][$i] ?? null,
                'price_per_person' => $price,
            ]);
            $added++;
        }

        return back()->with('status', $added > 0 ? "{$added} pricing tier(s) added." : 'Nothing to add.');
    }

    public function destroyPricingTier(Tour $tour, TourPricingTier $pricingTier): RedirectResponse
    {
        abort_unless($pricingTier->tour_id === $tour->id, 404);
        $pricingTier->delete();

        return back()->with('status', 'Pricing tier removed.');
    }
}
