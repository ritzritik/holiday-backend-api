<?php

use App\Http\Controllers\Admin\PendingDetailsController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Route::get('/test-stripe', [PendingDetailsController::class,'testStripe']);


require __DIR__.'/admin.php';
require __DIR__.'/booking_pending.php';
require __DIR__.'/transfer.php';
require __DIR__.'/post.php';


Route::get('/test-stripe', function() {
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    try {
        $charge = \Stripe\Charge::create([
            'amount' => 5000,
            'currency' => 'usd',
            'source' => 'tok_visa', // Use a test token
            'description' => 'Test Charge',
        ]);

        return response()->json($charge);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
