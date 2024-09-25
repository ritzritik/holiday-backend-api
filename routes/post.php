<?php

use App\Http\Controllers\Admin\TestimonialController;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PostController;

// use App\Http\Controllers\ProfileController;
// use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;



Route::prefix('admin')->group(function () {
    Route::get('/post', [PostController::class, 'index'])->name('admin.posts.index');
    Route::get('/post/create', [PostController::class, 'create'])->name('admin.posts.create');
    Route::post('/post/store', [PostController::class, 'store'])->name('admin.posts.store');
    Route::get('/post/{id}/edit', [PostController::class, 'edit'])->name('admin.posts.edit');
    Route::put('/post/{id}', [PostController::class, 'update'])->name('admin.posts.update');
    Route::delete('/post/{id}', [PostController::class, 'destroy'])->name('admin.posts.destroy');
    Route::get('/post/{id}', [PostController::class, 'show'])->name('admin.posts.show');

    Route::put('/post/{id}/approve', [PostController::class, 'approve'])->name('admin.posts.approve');
    Route::put('/post/{id}/reject', [PostController::class, 'reject'])->name('admin.posts.reject');
});

Route::prefix('admin')->group(function () {
    Route::get('/testimonial/publish', [TestimonialController::class, 'publish'])->name('admin.testimonial.publish');

    Route::get('/testimonial/draft', [TestimonialController::class, 'draft'])->name('admin.testimonial.draft');

    Route::get('/testimonial/create', [TestimonialController::class, 'create'])->name('admin.testimonial.create');

    Route::post('/testimonial', [TestimonialController::class, 'store'])->name('admin.testimonials.store');

    Route::get('/testimonial/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('admin.testimonials.edit');

    Route::put('/admin/testimonials/{id}', [TestimonialController::class, 'update'])->name('admin.testimonials.update');

    Route::delete('/testimonial/{testimonial}', [TestimonialController::class, 'destroy'])->name('admin.testimonials.destroy');

    Route::post('/testimonial/{id}/status/{status}', [TestimonialController::class, 'changeStatus'])->name('testimonial.changeStatus');

});

Route::get('/admin/testimonial/fetch_published', [TestimonialController::class, 'fetchPublished']);
