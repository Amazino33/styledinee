<?php

use App\Http\Controllers\AffiliateRegistrationController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\PrintOrderItemController;
use App\Livewire\Customer\Dashboard;
use App\Livewire\Customer\Login;
use App\Livewire\Customer\Orders;
use App\Livewire\Customer\Profile;
use App\Livewire\Customer\SetUsername;
use App\Livewire\Customer\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/print/order-item/{id}', PrintOrderItemController::class)->name('print.order-item');
});

// ── Affiliate registration (public) ──────────────────────────────────────────
Route::get('/affiliate/register',         [AffiliateRegistrationController::class, 'show'])->name('affiliate.register');
Route::post('/affiliate/register',        [AffiliateRegistrationController::class, 'submit'])->name('affiliate.register.submit');
Route::get('/affiliate/register/success', [AffiliateRegistrationController::class, 'success'])->name('affiliate.register.success');

// ── Customer portal ──────────────────────────────────────────────────────────
Route::prefix('account')->name('account.')->group(function () {

    // Public: login (phone → OTP → username)
    Route::get('/login', Login::class)->name('login');

    // Require login — username setup allowed without a username yet
    Route::middleware('auth.customer')->group(function () {
        Route::get('/username', SetUsername::class)->name('username');

        Route::post('/logout', function () {
            Auth::guard('customer_web')->logout();
            return redirect()->route('account.login');
        })->name('logout');
    });

    // Require login + username set
    Route::middleware(['auth.customer', 'customer.username'])->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
        Route::get('/orders',    Orders::class)->name('orders');
        Route::get('/wallet',    Wallet::class)->name('wallet');
        Route::get('/profile',   Profile::class)->name('profile');
    });
});

// !! TEMPORARY — delete this route immediately after use !!
Route::get('/setup-admin/{email}/{secret}', function (string $email, string $secret) {
    abort_if($secret !== 'Std@Setup2026!', 403);
    $user = \App\Models\User::where('email', $email)->firstOrFail();
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $user->assignRole('super_admin');
    return "Done. {$user->name} is now super_admin. DELETE THIS ROUTE NOW.";
});
// !! END TEMPORARY !!

Route::get('/',          [FrontendController::class, 'home'])->name('home');
Route::get('/services',  [FrontendController::class, 'services'])->name('services');
Route::get('/shop',      [FrontendController::class, 'shop'])->name('shop');
Route::get('/gallery',   [FrontendController::class, 'gallery'])->name('gallery');
Route::get('/contact',   [FrontendController::class, 'contact'])->name('contact');
Route::post('/contact',  [FrontendController::class, 'submitEnquiry'])->name('contact.submit');
