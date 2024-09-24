<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


require __DIR__.'/admin.php';
require __DIR__.'/booking_pending.php';
require __DIR__.'/transfer.php';
require __DIR__.'/api.php';
require __DIR__.'/post.php';
