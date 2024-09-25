<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BookingDetailsController;
use App\Http\Controllers\Admin\PendingDetailsController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;



Route::prefix('admin')->group(function () {
    Route::get('/booked', [BookingDetailsController::class, 'index'])->name('admin.booking-details');
    Route::get('/booked/packages', [BookingDetailsController::class, 'packages'])->name('admin.booking.packages');
    Route::get('/booked/flights', [BookingDetailsController::class, 'flights'])->name('admin.booking.flights');
    Route::get('/booked/hotels', [BookingDetailsController::class, 'hotels'])->name('admin.booking.hotels');
    Route::get('/booked/holidays', [BookingDetailsController::class, 'holidays'])->name('admin.booking.holidays');
})->middleware('auth');


Route::prefix('admin')->group(function () {
    Route::get('/admin/pending/{type}', [PendingDetailsController::class, 'loadPending'])->name('admin.pending.load');
    Route::get('/pending', [PendingDetailsController::class, 'index'])->name('admin.pending-details');
    Route::get('/pending/packages', [PendingDetailsController::class, 'packages'])->name('admin.pending.packages');
    Route::get('/pending/flights', [PendingDetailsController::class, 'flights'])->name('admin.pending.flights');
    Route::get('/pending/hotels', [PendingDetailsController::class, 'hotels'])->name('admin.pending.hotels');
    Route::get('/pending/holidays', [PendingDetailsController::class, 'holidays'])->name('admin.pending.holidays');
})->middleware('auth');


Route::prefix('admin')->group(function () {
    Route::get('/payments', [PendingDetailsController::class, 'payment'])->name('admin.payments-details');
    Route::post('/payments/accept', [PendingDetailsController::class, 'accept'])->name('admin.payments.accept');
    Route::post('/payments/reject', [PendingDetailsController::class, 'reject'])->name('admin.payments.reject');
    Route::post('/admin/payments/approve', [PendingDetailsController::class, 'approve'])->name('admin.payments.approve');
})->middleware('auth');
