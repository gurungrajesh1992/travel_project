<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReportController extends Controller
{
    private const PRESETS = [
        'this_month' => 'This Month',
        'last_month' => 'Last Month',
        'this_year' => 'This Year',
        'last_12_months' => 'Last 12 Months',
        'all_time' => 'All Time',
        'custom' => 'Custom Range',
    ];

    public function sales(Request $request): View
    {
        [$from, $to, $label, $preset] = $this->resolveDateRange($request);

        $inRange = fn () => Booking::whereBetween('created_at', [$from, $to]);
        $currency = $this->currency();

        $totalBookings = $inRange()->count();
        $activeBookings = $inRange()->where('booking_status', '!=', 'cancelled');
        $totalRevenue = (clone $activeBookings)->sum('total_amount');
        $totalDiscount = (clone $activeBookings)->sum('discount_amount');
        $activeCount = (clone $activeBookings)->count();
        $avgBookingValue = $activeCount > 0 ? $totalRevenue / $activeCount : 0;

        $groupByMonth = $from->diffInDays($to) > 62;
        $format = $groupByMonth ? '%Y-%m' : '%Y-%m-%d';

        $trend = (clone $activeBookings)
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as period, SUM(total_amount) as revenue, COUNT(*) as bookings")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $paymentBreakdown = $inRange()
            ->selectRaw('payment_status, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_status')
            ->get();

        $chartData = [
            'type' => 'line',
            'data' => [
                'labels' => $trend->pluck('period')->all(),
                'datasets' => [[
                    'label' => 'Revenue',
                    'data' => $trend->pluck('revenue')->map(fn ($v) => (float) $v)->all(),
                    'borderColor' => '#2563eb',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.12)',
                    'fill' => true,
                    'tension' => 0.3,
                ]],
            ],
            'options' => [
                'plugins' => ['legend' => ['display' => false]],
                'scales' => ['y' => ['beginAtZero' => true]],
            ],
        ];

        return view('admin.reports.sales', [
            'from' => $from, 'to' => $to, 'label' => $label, 'preset' => $preset, 'presets' => self::PRESETS,
            'currency' => $currency, 'totalBookings' => $totalBookings, 'totalRevenue' => $totalRevenue,
            'totalDiscount' => $totalDiscount, 'avgBookingValue' => $avgBookingValue,
            'paymentBreakdown' => $paymentBreakdown, 'chartData' => $chartData,
        ]);
    }

    public function tours(Request $request): View
    {
        [$from, $to, $label, $preset] = $this->resolveDateRange($request);
        $currency = $this->currency();

        $tours = Tour::query()
            ->withCount(['bookings' => fn ($q) => $q->whereBetween('created_at', [$from, $to])->where('booking_status', '!=', 'cancelled')])
            ->withSum(['bookings as revenue_sum' => fn ($q) => $q->whereBetween('created_at', [$from, $to])->where('booking_status', '!=', 'cancelled')], 'total_amount')
            ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->having('bookings_count', '>', 0)
            ->orderByDesc('bookings_count')
            ->limit(20)
            ->get();

        $top10 = $tours->take(10);

        $chartData = [
            'type' => 'bar',
            'data' => [
                'labels' => $top10->pluck('title')->all(),
                'datasets' => [[
                    'label' => 'Bookings',
                    'data' => $top10->pluck('bookings_count')->all(),
                    'backgroundColor' => '#2563eb',
                ]],
            ],
            'options' => [
                'indexAxis' => 'y',
                'plugins' => ['legend' => ['display' => false]],
                'scales' => ['x' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
            ],
        ];

        return view('admin.reports.tours', [
            'from' => $from, 'to' => $to, 'label' => $label, 'preset' => $preset, 'presets' => self::PRESETS,
            'currency' => $currency, 'tours' => $tours, 'chartData' => $chartData,
        ]);
    }

    public function bookings(Request $request): View
    {
        $period = $request->query('period') === 'yearly' ? 'yearly' : 'monthly';
        $currency = $this->currency();

        if ($period === 'yearly') {
            $yearsBack = 4;
            $startYear = now()->year - $yearsBack;
            $end = now()->endOfYear();
            $start = Carbon::createFromDate($startYear, 1, 1)->startOfYear();

            $raw = Booking::whereBetween('created_at', [$start, $end])
                ->selectRaw("YEAR(created_at) as period, COUNT(*) as total, SUM(CASE WHEN booking_status != 'cancelled' THEN total_amount ELSE 0 END) as revenue, SUM(CASE WHEN booking_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled")
                ->groupBy('period')
                ->get()
                ->keyBy('period');

            $rows = collect(range($startYear, now()->year))->map(fn ($year) => (object) [
                'label' => (string) $year,
                'total' => (int) ($raw[$year]->total ?? 0),
                'revenue' => (float) ($raw[$year]->revenue ?? 0),
                'cancelled' => (int) ($raw[$year]->cancelled ?? 0),
            ]);

            $year = null;
        } else {
            $year = (int) $request->query('year', now()->year);
            $start = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $end = Carbon::createFromDate($year, 12, 31)->endOfYear();

            $raw = Booking::whereBetween('created_at', [$start, $end])
                ->selectRaw("MONTH(created_at) as period, COUNT(*) as total, SUM(CASE WHEN booking_status != 'cancelled' THEN total_amount ELSE 0 END) as revenue, SUM(CASE WHEN booking_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled")
                ->groupBy('period')
                ->get()
                ->keyBy('period');

            $rows = collect(range(1, 12))->map(fn ($month) => (object) [
                'label' => Carbon::create()->month($month)->format('M'),
                'total' => (int) ($raw[$month]->total ?? 0),
                'revenue' => (float) ($raw[$month]->revenue ?? 0),
                'cancelled' => (int) ($raw[$month]->cancelled ?? 0),
            ]);
        }

        $chartData = [
            'type' => 'bar',
            'data' => [
                'labels' => $rows->pluck('label')->all(),
                'datasets' => [[
                    'label' => 'Bookings',
                    'data' => $rows->pluck('total')->all(),
                    'backgroundColor' => '#2563eb',
                ]],
            ],
            'options' => [
                'plugins' => ['legend' => ['display' => false]],
                'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
            ],
        ];

        return view('admin.reports.bookings', [
            'period' => $period, 'year' => $year, 'currentYear' => now()->year,
            'currency' => $currency, 'rows' => $rows, 'chartData' => $chartData,
        ]);
    }

    public function customers(Request $request): View
    {
        [$from, $to, $label, $preset] = $this->resolveDateRange($request);
        $currency = $this->currency();

        $rows = Booking::whereBetween('created_at', [$from, $to])
            ->where('booking_status', '!=', 'cancelled')
            ->selectRaw('user_id, guest_name, guest_email, COALESCE(CAST(user_id AS CHAR), guest_email) as group_key, COUNT(*) as bookings_count, SUM(total_amount) as total_spent, MAX(created_at) as last_booking_at')
            ->groupBy('group_key', 'user_id', 'guest_name', 'guest_email')
            ->orderByDesc('total_spent')
            ->limit(50)
            ->get();

        $users = User::whereIn('id', $rows->pluck('user_id')->filter()->unique())->get(['id', 'name', 'email'])->keyBy('id');

        $rows = $rows->map(function ($row) use ($users) {
            $user = $row->user_id ? $users->get($row->user_id) : null;
            $row->display_name = $user?->name ?? $row->guest_name ?? 'Guest';
            $row->email = $user?->email ?? $row->guest_email;
            $row->is_registered = (bool) $user;

            return $row;
        });

        $customerCount = $rows->count();
        $repeatCustomers = $rows->where('bookings_count', '>', 1)->count();
        $totalRevenue = $rows->sum('total_spent');
        $avgBookingsPerCustomer = $customerCount > 0 ? $rows->sum('bookings_count') / $customerCount : 0;

        return view('admin.reports.customers', [
            'from' => $from, 'to' => $to, 'label' => $label, 'preset' => $preset, 'presets' => self::PRESETS,
            'currency' => $currency, 'rows' => $rows, 'customerCount' => $customerCount,
            'repeatCustomers' => $repeatCustomers, 'totalRevenue' => $totalRevenue,
            'avgBookingsPerCustomer' => $avgBookingsPerCustomer,
        ]);
    }

    public function cancellations(Request $request): View
    {
        [$from, $to, $label, $preset] = $this->resolveDateRange($request);
        $currency = $this->currency();

        $bookingsInRange = Booking::whereBetween('created_at', [$from, $to]);
        $totalBookings = (clone $bookingsInRange)->count();

        $cancelledBookings = (clone $bookingsInRange)
            ->where('booking_status', 'cancelled')
            ->with('tour:id,title')
            ->orderByDesc('cancelled_at')
            ->get();

        $cancelledCount = $cancelledBookings->count();
        $cancellationRate = $totalBookings > 0 ? round(($cancelledCount / $totalBookings) * 100, 1) : 0;
        $lostRevenue = $cancelledBookings->sum('total_amount');

        $topReasons = $cancelledBookings
            ->groupBy(fn ($b) => $b->cancellation_reason ? Str::limit($b->cancellation_reason, 60) : 'No reason given')
            ->map(fn ($group, $reason) => ['reason' => $reason, 'count' => $group->count()])
            ->sortByDesc('count')
            ->take(5)
            ->values();

        $groupByMonth = $from->diffInDays($to) > 62;
        $format = $groupByMonth ? '%Y-%m' : '%Y-%m-%d';

        $trend = (clone $bookingsInRange)
            ->where('booking_status', 'cancelled')
            ->selectRaw("DATE_FORMAT(cancelled_at, '{$format}') as period, COUNT(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $chartData = [
            'type' => 'bar',
            'data' => [
                'labels' => $trend->pluck('period')->all(),
                'datasets' => [[
                    'label' => 'Cancellations',
                    'data' => $trend->pluck('count')->all(),
                    'backgroundColor' => '#dc2626',
                ]],
            ],
            'options' => [
                'plugins' => ['legend' => ['display' => false]],
                'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
            ],
        ];

        return view('admin.reports.cancellations', [
            'from' => $from, 'to' => $to, 'label' => $label, 'preset' => $preset, 'presets' => self::PRESETS,
            'currency' => $currency, 'totalBookings' => $totalBookings, 'cancelledCount' => $cancelledCount,
            'cancellationRate' => $cancellationRate, 'lostRevenue' => $lostRevenue, 'topReasons' => $topReasons,
            'cancelledBookings' => $cancelledBookings, 'chartData' => $chartData,
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string, 3: string}
     */
    private function resolveDateRange(Request $request): array
    {
        $preset = $request->query('preset', 'this_month');
        $today = now();

        return match ($preset) {
            'last_month' => [
                $today->copy()->subMonthNoOverflow()->startOfMonth(),
                $today->copy()->subMonthNoOverflow()->endOfMonth(),
                self::PRESETS['last_month'], 'last_month',
            ],
            'this_year' => [$today->copy()->startOfYear(), $today->copy()->endOfYear(), self::PRESETS['this_year'], 'this_year'],
            'last_12_months' => [
                $today->copy()->subMonthsNoOverflow(11)->startOfMonth(), $today->copy()->endOfMonth(),
                self::PRESETS['last_12_months'], 'last_12_months',
            ],
            'all_time' => [Carbon::createFromDate(2000, 1, 1), $today->copy()->endOfDay(), self::PRESETS['all_time'], 'all_time'],
            'custom' => [
                $request->filled('from') ? Carbon::parse($request->query('from'))->startOfDay() : $today->copy()->startOfMonth(),
                $request->filled('to') ? Carbon::parse($request->query('to'))->endOfDay() : $today->copy()->endOfDay(),
                self::PRESETS['custom'], 'custom',
            ],
            default => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth(), self::PRESETS['this_month'], 'this_month'],
        };
    }

    private function currency(): string
    {
        return Tour::query()->value('currency') ?? 'USD';
    }
}
