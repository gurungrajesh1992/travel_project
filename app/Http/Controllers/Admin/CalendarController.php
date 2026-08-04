<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourDeparture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CalendarController extends Controller
{
    private const STATUS_COLORS = [
        'open' => '#16a34a',
        'full' => '#ca8a04',
        'cancelled' => '#dc2626',
    ];

    public function index(): View
    {
        $today = now()->toDateString();

        $upcomingDepartures = TourDeparture::query()
            ->with('tour:id,title,slug,currency')
            ->where('departure_date', '>', $today)
            ->where('status', '!=', 'cancelled')
            ->orderBy('departure_date')
            ->limit(15)
            ->get();

        $runningDepartures = TourDeparture::query()
            ->with('tour:id,title,slug,currency')
            ->where('departure_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->where('return_date', '>=', $today)
                    ->orWhere(function ($q2) use ($today) {
                        $q2->whereNull('return_date')->where('departure_date', $today);
                    });
            })
            ->where('status', '!=', 'cancelled')
            ->orderBy('departure_date')
            ->get();

        return view('admin.calendar.index', [
            'upcomingDepartures' => $upcomingDepartures,
            'runningDepartures' => $runningDepartures,
        ]);
    }

    public function events(Request $request): JsonResponse
    {
        $start = Carbon::parse($request->query('start'))->toDateString();
        $end = Carbon::parse($request->query('end'))->toDateString();

        $departures = TourDeparture::query()
            ->with('tour:id,title,slug')
            ->where('departure_date', '<', $end)
            ->where(function ($q) use ($start) {
                $q->where('return_date', '>=', $start)
                    ->orWhere(function ($q2) use ($start) {
                        $q2->whereNull('return_date')->where('departure_date', '>=', $start);
                    });
            })
            ->get();

        return response()->json($departures->map(function (TourDeparture $departure) {
            $exclusiveEnd = ($departure->return_date ?? $departure->departure_date)->copy()->addDay();

            return [
                'title' => $departure->tour->title,
                'start' => $departure->departure_date->toDateString(),
                'end' => $exclusiveEnd->toDateString(),
                'url' => route('admin.tours.edit', $departure->tour),
                'color' => self::STATUS_COLORS[$departure->status] ?? '#6b7280',
                'extendedProps' => ['status' => $departure->status],
            ];
        })->values());
    }
}
