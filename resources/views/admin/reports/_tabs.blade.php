@php
    $reportTabs = [
        'admin.reports.sales' => 'Sales & Revenue',
        'admin.reports.tours' => 'Popular Tours',
        'admin.reports.bookings' => 'Monthly / Yearly',
        'admin.reports.customers' => 'Customers',
        'admin.reports.cancellations' => 'Cancellations',
    ];
@endphp

<div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200 pb-3">
    @foreach ($reportTabs as $route => $tabLabel)
        <a href="{{ route($route) }}"
           class="px-3 py-1.5 text-sm font-medium rounded-md {{ request()->routeIs($route) ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }}">
            {{ $tabLabel }}
        </a>
    @endforeach
</div>
