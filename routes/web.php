<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\PrintOrderItemController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/print/order-item/{id}', PrintOrderItemController::class)->name('print.order-item');
});

Route::get('/',          [FrontendController::class, 'home'])->name('home');
Route::get('/services',  [FrontendController::class, 'services'])->name('services');
Route::get('/shop',      [FrontendController::class, 'shop'])->name('shop');
Route::get('/gallery',   [FrontendController::class, 'gallery'])->name('gallery');
Route::get('/contact',   [FrontendController::class, 'contact'])->name('contact');
Route::post('/contact',  [FrontendController::class, 'submitEnquiry'])->name('contact.submit');
