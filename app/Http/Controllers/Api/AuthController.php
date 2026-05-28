<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Models\Username;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // POST /api/auth/request-otp
    public function requestOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'min:7'],
        ]);

        $phone = Customer::normalizePhone($request->phone);

        CustomerOtp::where('phone', $phone)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->update(['expires_at' => now()]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        CustomerOtp::create([
            'phone'      => $phone,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        app(NotificationService::class)->sendAuthOtp($phone, $otp);

        $response = ['message' => 'OTP sent to ' . $phone];

        if (app()->isLocal()) {
            $response['otp'] = $otp;
        }

        return response()->json($response);
    }

    // POST /api/auth/verify-otp
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone'               => ['required', 'string'],
            'otp'                 => ['required', 'string', 'size:6'],
            'name'                => ['sometimes', 'string', 'max:255'],
            'username'            => ['sometimes', 'nullable', 'string', 'max:50', 'alpha_dash', 'unique:customers,username', 'unique:users,username', 'unique:affiliates,username'],
            'referred_by_username' => ['sometimes', 'nullable', 'string', 'max:50'],
        ]);

        $phone = Customer::normalizePhone($request->phone);

        $record = CustomerOtp::where('phone', $phone)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record || $record->otp !== $request->otp) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }

        $record->update(['verified_at' => now()]);

        $customer = DB::transaction(function () use ($request, $phone) {
            $existing = Customer::where('phone', $phone)->first();

            if ($existing) {
                return $existing;
            }

            // Validate referral code if provided
            $referredBy = null;
            if ($request->filled('referred_by_username')) {
                $referredBy = Username::find($request->referred_by_username)
                    ? $request->referred_by_username
                    : null;
            }

            $customer = Customer::create([
                'phone'                => $phone,
                'name'                 => $request->name ?? 'Customer',
                'username'             => $request->username ?? null,
                'referred_by_username' => $referredBy,
            ]);

            // Register username in global registry if provided
            if ($customer->username) {
                Username::claim($customer->username, 'customer', $customer->id);
            }

            return $customer;
        });

        $customer->tokens()->delete();
        $token = $customer->createToken('customer-app')->plainTextToken;

        return response()->json([
            'token'          => $token,
            'needs_username' => $customer->needsUsername(),
            'customer'       => [
                'id'       => $customer->id,
                'name'     => $customer->name,
                'phone'    => $customer->phone,
                'email'    => $customer->email,
                'username' => $customer->username,
            ],
        ]);
    }

    // POST /api/auth/set-username
    public function setUsername(Request $request): JsonResponse
    {
        $request->validate([
            'username' => [
                'required', 'string', 'max:50', 'alpha_dash',
                'unique:customers,username',
                'unique:users,username',
                'unique:affiliates,username',
            ],
        ]);

        $customer = $request->user('customer');

        if (! $customer->needsUsername()) {
            return response()->json(['message' => 'Username already set.'], 422);
        }

        // Check global registry too
        if (! Username::isAvailable($request->username)) {
            return response()->json(['message' => 'This username is already taken.'], 422);
        }

        DB::transaction(function () use ($customer, $request) {
            $customer->update(['username' => $request->username]);
            Username::claim($request->username, 'customer', $customer->id);
        });

        return response()->json([
            'message'  => 'Username set successfully.',
            'username' => $customer->username,
        ]);
    }

    // POST /api/auth/logout
    public function logout(Request $request): JsonResponse
    {
        $request->user('customer')->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }
}
