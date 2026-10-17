<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\Dashboard\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'home'])->name('home');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('dashboard/posts')->name('dashboard.posts.')->controller(PostController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::post('/images', 'uploadImage')->name('images.store');
        Route::get('/{post}/edit', 'edit')->name('edit');
        Route::put('/{post}', 'update')->name('update');
        Route::delete('/{post}', 'destroy')->name('destroy');
    });
});

require __DIR__.'/settings.php';
