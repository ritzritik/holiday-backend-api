<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\TransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/admin/transfer', [TransferController::class, 'index'])->name('admin.transfer');
Route::post('/admin/transfer/store', [TransferController::class, 'store'])->name('admin.transfer.store');

Route::get('/admin/transfer/fetchPricing', [TransferController::class, 'fetchPricing'])->name('admin.transfer.fetchPricing');

Route::get('/api/regions/{countryId}', [TransferController::class, 'fetchRegions'])->name('admin.transfer.fetchRegions');

Route::get('/admin/parking', [TransferController::class, 'parking'])->name('admin.parking');
Route::get('/admin/getPricing/{airportId}', [TransferController::class, 'getPricing']);
Route::post('/admin/setPricing', [TransferController::class, 'setPricing'])->name('admin.setPricing');

Route::get('/admin/insurance', [TransferController::class, 'insurance'])->name('admin.insurance');
Route::get('/admin/luggage', [TransferController::class, 'luggage'])->name('admin.luggage');
Route::get('/admin/history', [TransferController::class, 'history'])->name('admin.history');
Route::get('admin/subscribers', [TransferController::class, 'subscribers'])->name('admin.subscribers');
Route::post('/admin/send-email', 'AdminController@sendEmail');
