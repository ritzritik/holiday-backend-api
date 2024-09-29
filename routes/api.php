<?php

use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TransferController;
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
    Route::get('/all-bookings', [AuthController::class, 'all_bookings']);
    Route::post('/contact/admin', [AuthController::class, 'contact_admin']);
    Route::post('/profile/update', [AuthController::class, 'profile_update']);
    Route::post('/profile/delete', [AuthController::class, 'profile_delete']);
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

Route::post('/package/save-passenger', [PackagesController::class, 'storePassengerDetails']);

Route::post('/package/save-booking-details', [PackagesController::class, 'saveBookingDetails']);

Route::post('/package/verify-promo-code', [PackagesController::class, 'verifyPromoCode']);

Route::post('/package/details-verify', [PackagesController::class, 'saveCardDetails']);


// Route::get('/flights', [FlightsController::class, 'index'])->name('flight.index');

Route::get('/flights/search', [FlightsController::class, 'flightSearch']);

Route::post('/flight/booking', [FlightsController::class, 'flight_booking_details']);

Route::post('/flights/alternate/search', [FlightsController::class, 'alternateFlights']);

Route::get('/flight/checkout', [FlightsController::class, 'flight_checkout']);

// Route::get('/hotels', [HotelsController::class, 'index'])->name('hotel.index');
Route::get('/hotels/search', [HotelsController::class, 'hotelSearch']);

Route::post('/hotel/booking/details', [HotelsController::class, 'hotel_booking_details']);

Route::get('/hotel/checkout', [HotelsController::class, 'hotel_checkout']);

Route::get('/skiholidays', [SkiHolidaysController::class, 'index']);
Route::get('/ski/search', [SkiHolidaysController::class, 'ski_holiday_search']);

Route::post('/ski/booking', [SkiHolidaysController::class, 'ski_booking_details']);

Route::get('/ski/checkout', [SkiHolidaysController::class, 'ski_checkout']);

Route::get('/testimonial/fetch_published', [TestimonialController::class, 'fetchPublished']);

Route::get('/getPricing/{airportId}', [TransferController::class, 'getPricing']);

