<?php

use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\InquiryController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\WishlistController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

    Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries.index');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::delete('/wishlist/{tour}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});
