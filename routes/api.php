<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotelsController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\SkiHolidaysController;
use App\Http\Controllers\FlightsController;
use App\Http\Controllers\AuthController;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protect routes using JWT middleware
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});


Route::post('/test', function (Request $request) {
    return response()->json([
        'data' => $request->all()
    ]);
});


// Route for fetching data for the home page
Route::get('/home', [HomeController::class, 'index']);

// Route for subscribing to the newsletter
Route::post('/subscribe', [HomeController::class, 'subscribe']);

// Route for fetching all packages with optional filtering
Route::get('/packages', [PackagesController::class, 'index']);

// Route for searching packages with dynamic query parameters
Route::get('/packages/search', [PackagesController::class, 'packageSearch']);


Route::get('/package/theme', [PackagesController::class, 'packages_by_theme']);

Route::post('/package/booking/details', [PackagesController::class, 'package_booking_details']);

// Route for handling checkout
Route::get('/package/checkout', [PackagesController::class, 'package_checkout']);

// Route::get('/flights', [FlightsController::class, 'index'])->name('flight.index');

Route::post('/flights/search', [FlightsController::class, 'flightSearch'])->name('flight.search');

Route::post('/flights/booking', [FlightsController::class, 'bookingDetails'])->name('flight.booking-details');

Route::post('/flights/alternate/search', [FlightsController::class, 'alternateFlights'])->name('flight.alternate');

Route::post('/flights/payment/checkout', [FlightsController::class, 'flightPayment'])->name('flight.payment');

Route::get('/hotels', [HotelsController::class, 'index'])->name('hotel.index');
Route::get('/hotels/search', [HotelsController::class, 'hotel_search'])->name('hotel.search');

Route::post('/hotel/booking', [HotelsController::class, 'hotel_booking_details'])->name('hotel.hotel-booking-details');

Route::get('/skiholidays', [SkiHolidaysController::class, 'index'])->name('ski.index');
Route::get('/ski/search', [SkiHolidaysController::class, 'ski_holiday_search'])->name('ski.search');

Route::post('/ski/booking', [SkiHolidaysController::class, 'ski_booking_details'])->name('ski.ski-booking-details');

Route::post('/ski/checkout', [SkiHolidaysController::class, 'ski_checkout'])->name('ski.checkout');

