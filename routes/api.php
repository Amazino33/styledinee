<?php

use App\Http\Controllers\Api\AffiliateController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('request-otp', [AuthController::class, 'requestOtp']);
    Route::post('verify-otp',  [AuthController::class, 'verifyOtp']);
});

Route::get('referral/validate/{code}', [AffiliateController::class, 'validateCode']);

// ── Protected: customer must be authenticated ─────────────────────────────────
Route::middleware('auth:customer')->group(function () {
    // Auth
    Route::post('auth/logout',      [AuthController::class, 'logout']);
    Route::post('auth/set-username', [AuthController::class, 'setUsername']);

    // Orders
    Route::get('orders',               [OrderController::class, 'index']);
    Route::get('orders/{reference}',   [OrderController::class, 'show']);

    // Profile
    Route::get('profile',              [ProfileController::class, 'show']);
    Route::put('profile',              [ProfileController::class, 'update']);
    Route::get('profile/measurements', [ProfileController::class, 'measurements']);

    // Wallet (referral credit balance + ledger)
    Route::get('me/wallet',            [WalletController::class, 'show']);

    // Referral & affiliate
    Route::get('me/referral',          [AffiliateController::class, 'myReferral']);
    Route::post('me/apply-affiliate',  [AffiliateController::class, 'apply']);

    // Coupons
    Route::get('coupons/validate/{code}', [CouponController::class, 'validate']);
});
