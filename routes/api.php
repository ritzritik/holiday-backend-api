<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PackagesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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
Route::post('/packages/search', [PackagesController::class, 'packageSearch']);


Route::get('/package/theme', [PackagesController::class, 'packages_by_theme']);

Route::post('/package/booking/details', [PackagesController::class, 'package_booking_details']);

// Route for handling checkout
Route::post('/package/checkout', [PackagesController::class, 'package_checkout']);