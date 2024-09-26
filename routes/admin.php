<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Route::get('/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
// Route::post('/login', [LoginController::class, 'adminLogin']);

Route::middleware('guest.admin')->group(function () {
    Route::get('/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'adminLogin']);
});
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['check.admin', 'guest.admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::get('/admin/profile/edit/{id}', [AdminController::class, 'edit'])->name('admin.profile.edit');
    Route::post('/admin/profile/update/{id}', [AdminController::class, 'update'])->name('admin.profile.update');

    Route::get('/admin/user/create', [UserController::class, 'create'])->name('admin.user.create');
    Route::post('/admin/user/create', [UserController::class, 'store'])->name('admin.user.store');
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.user.index');
    Route::get('/admin/user/edit/{id}', [UserController::class, 'edit'])->name('admin.user.edit');
    Route::post('/admin/user/update/{id}', [UserController::class, 'update'])->name('admin.user.update');
    Route::patch('/admin/user/delete/{id}', [UserController::class, 'destroy'])->name('admin.user.destroy');
    Route::delete('/admin/user/delete/{id}', [UserController::class, 'delete'])->name('admin.user.delete');
    Route::patch('/admin/trash/user/restore/{id}', [UserController::class, 'restore'])->name('admin.trash.user.restore');
    Route::get('/admin/user/trash', [UserController::class, 'trash'])->name('admin.user.trash');

    Route::get('/admin/registeredUser', [AdminController::class, 'getRegisteredUser'])->name('admin.registered-user');
});



Route::prefix('admin')->group(function () {
    Route::get('/coupon', [CouponController::class, 'index'])->name('admin.coupon.index');
    Route::get('/coupon/create', [CouponController::class, 'create'])->name('admin.coupon.create');
    Route::post('/coupon/create', [CouponController::class, 'store'])->name('admin.coupon.store');
    Route::get('/coupon/{id}/edit', [CouponController::class, 'edit'])->name('admin.coupon.edit');
    Route::put('/coupon/{id}', [CouponController::class, 'update'])->name('admin.coupon.update');
    Route::delete('/coupon/{id}', [CouponController::class, 'destroy'])->name('admin.coupon.destroy');
    Route::get('/coupon/trash', [CouponController::class, 'trash'])->name('admin.coupon.trash');
    Route::patch('/coupon/trash/restore/{id}', [CouponController::class, 'restore'])->name('admin.coupon.restore');
    Route::delete('/coupon/trash/delete/{id}', [CouponController::class, 'permanentDelete'])->name('admin.coupon.delete');
});

// Route::middleware(['guest.admin'])->group(function () {
//     Route::get('/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
// });
