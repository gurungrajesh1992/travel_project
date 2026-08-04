<?php

use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CompanySettingsController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\DifficultyLevelController;
use App\Http\Controllers\Admin\GuideController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ThemeSettingsController;
use App\Http\Controllers\Admin\TourContentController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\TourItineraryController;
use App\Http\Controllers\Admin\TourMediaController;
use App\Http\Controllers\Admin\TourPricingController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->get('login', [AuthenticatedSessionController::class, 'create'])->name('login');

Route::middleware(['auth', 'role:admin|staff'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('destinations', DestinationController::class)->except('show');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('difficulty-levels', DifficultyLevelController::class)->except('show');
    Route::resource('tours', TourController::class);
    Route::resource('coupons', CouponController::class)->except('show');
    Route::resource('guides', GuideController::class)->except('show');

    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('{customer}', [CustomerController::class, 'show'])->name('show');
        Route::patch('{customer}/suspend', [CustomerController::class, 'suspend'])->name('suspend');
        Route::patch('{customer}/activate', [CustomerController::class, 'activate'])->name('activate');
    });

    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('{booking}', [BookingController::class, 'show'])->name('show');
        Route::patch('{booking}/status', [BookingController::class, 'updateStatus'])->name('status');
        Route::patch('{booking}/guide', [BookingController::class, 'assignGuide'])->name('guide');
        Route::patch('payments/{payment}', [BookingController::class, 'updatePayment'])->name('payments.update');
    });

    Route::prefix('inquiries')->name('inquiries.')->group(function () {
        Route::get('/', [InquiryController::class, 'index'])->name('index');
        Route::get('{inquiry}', [InquiryController::class, 'show'])->name('show');
        Route::post('{inquiry}/reply', [InquiryController::class, 'reply'])->name('reply');
        Route::patch('{inquiry}/status', [InquiryController::class, 'updateStatus'])->name('status');
        Route::delete('{inquiry}', [InquiryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::patch('{review}/approve', [ReviewController::class, 'approve'])->name('approve');
        Route::patch('{review}/unapprove', [ReviewController::class, 'unapprove'])->name('unapprove');
        Route::delete('{review}', [ReviewController::class, 'destroy'])->name('destroy');
    });

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    Route::prefix('reports')->name('reports.')->middleware('permission:view reports')->group(function () {
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('tours', [ReportController::class, 'tours'])->name('tours');
        Route::get('bookings', [ReportController::class, 'bookings'])->name('bookings');
        Route::get('customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('cancellations', [ReportController::class, 'cancellations'])->name('cancellations');
    });

    Route::prefix('tours/{tour}')->name('tours.')->group(function () {
        Route::post('highlights', [TourContentController::class, 'storeHighlight'])->name('highlights.store');
        Route::delete('highlights/{highlight}', [TourContentController::class, 'destroyHighlight'])->name('highlights.destroy');

        Route::post('cost-details', [TourContentController::class, 'storeCostDetail'])->name('cost-details.store');
        Route::delete('cost-details/{costDetail}', [TourContentController::class, 'destroyCostDetail'])->name('cost-details.destroy');

        Route::post('faqs', [TourContentController::class, 'storeFaq'])->name('faqs.store');
        Route::delete('faqs/{faq}', [TourContentController::class, 'destroyFaq'])->name('faqs.destroy');

        Route::post('itineraries', [TourItineraryController::class, 'store'])->name('itineraries.store');
        Route::delete('itineraries/{itinerary}', [TourItineraryController::class, 'destroy'])->name('itineraries.destroy');
        Route::post('itineraries/{itinerary}/media', [TourItineraryController::class, 'storeMedia'])->name('itineraries.media.store');
        Route::delete('itineraries/{itinerary}/media/{media}', [TourItineraryController::class, 'destroyMedia'])->name('itineraries.media.destroy');

        Route::post('departures', [TourPricingController::class, 'storeDeparture'])->name('departures.store');
        Route::delete('departures/{departure}', [TourPricingController::class, 'destroyDeparture'])->name('departures.destroy');

        Route::post('seasonal-pricing', [TourPricingController::class, 'storeSeasonalPrice'])->name('seasonal-pricing.store');
        Route::delete('seasonal-pricing/{seasonalPricing}', [TourPricingController::class, 'destroySeasonalPrice'])->name('seasonal-pricing.destroy');

        Route::post('pricing-tiers', [TourPricingController::class, 'storePricingTier'])->name('pricing-tiers.store');
        Route::delete('pricing-tiers/{pricingTier}', [TourPricingController::class, 'destroyPricingTier'])->name('pricing-tiers.destroy');

        Route::post('media', [TourMediaController::class, 'store'])->name('media.store');
        Route::delete('media/{media}', [TourMediaController::class, 'destroy'])->name('media.destroy');
    });

    Route::get('/settings/theme', [ThemeSettingsController::class, 'edit'])->name('settings.theme');
    Route::put('/settings/theme', [ThemeSettingsController::class, 'update'])->name('settings.theme.update');

    Route::get('/settings/company', [CompanySettingsController::class, 'edit'])->name('settings.company');
    Route::put('/settings/company', [CompanySettingsController::class, 'update'])->name('settings.company.update');
});
