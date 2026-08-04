<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TourController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'store'])->name('newsletter.subscribe');

Route::get('/multi-country', [DestinationController::class, 'multiCountry'])->name('destinations.multi-country');
Route::get('/destinations/{destination:slug}/{category:slug}', [DestinationController::class, 'category'])
    ->name('destinations.category')
    ->withoutScopedBindings();
Route::get('/destinations/{destination:slug}', [DestinationController::class, 'show'])->name('destinations.show');

Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
Route::get('/tours/{tour:slug}', [TourController::class, 'show'])->name('tours.show');
Route::post('/tours/{tour:slug}/reviews', [ReviewController::class, 'store'])->name('tours.reviews.store');
Route::post('/tours/{tour:slug}/inquiries', [InquiryController::class, 'store'])->name('tours.inquiries.store');
Route::post('/tours/{tour:slug}/book', [BookingController::class, 'store'])->name('tours.book');

Route::get('/bookings/{booking:booking_ref}/confirmation', [BookingController::class, 'confirmation'])->name('bookings.confirmation');
Route::post('/bookings/{booking:booking_ref}/payment', [BookingController::class, 'uploadPayment'])->name('bookings.payment');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/guides', [GuideController::class, 'index'])->name('guides.index');
Route::get('/guides/{guide:slug}', [GuideController::class, 'show'])->name('guides.show');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

Route::post('/contact-us', [InquiryController::class, 'store'])->name('pages.contact.store');
Route::get('/about-us', [PageController::class, 'show'])->defaults('slug', 'about-us')->name('pages.about');
Route::get('/contact-us', [PageController::class, 'show'])->defaults('slug', 'contact-us')->name('pages.contact');

require __DIR__.'/auth.php';
