<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AppSetting;
use App\Models\Username;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    // GET /api/referral/validate/{code}
    public function validateCode(string $code): JsonResponse
    {
        $exists = Username::where('username', $code)->exists();

        if (! $exists) {
            return response()->json(['valid' => false, 'message' => 'Referral code not found.'], 404);
        }

        return response()->json(['valid' => true, 'code' => $code]);
    }

    // POST /api/me/apply-affiliate — existing customer applies to become an affiliate
    public function apply(Request $request): JsonResponse
    {
        if (! AppSetting::bool('affiliate_registration_open', true)) {
            return response()->json(['message' => 'Affiliate registration is currently closed.'], 403);
        }

        $customer = $request->user('customer');

        if (! $customer->username) {
            return response()->json(['message' => 'You must set a username before applying.'], 422);
        }

        $existing = Affiliate::where('customer_id', $customer->id)->first();
        if ($existing) {
            return response()->json([
                'message' => 'You already have an affiliate application.',
                'status'  => $existing->status,
            ], 422);
        }

        $request->validate([
            'bank_name'      => ['sometimes', 'string', 'max:100'],
            'account_number' => ['sometimes', 'string', 'max:20'],
            'account_name'   => ['sometimes', 'string', 'max:150'],
        ]);

        $affiliate = Affiliate::create([
            'username'    => $customer->username,
            'name'        => $customer->name,
            'email'       => $customer->email,
            'phone'       => $customer->phone,
            'customer_id' => $customer->id,
            'bank_name'      => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name'   => $request->account_name,
            'status'      => 'pending',
        ]);

        return response()->json([
            'message' => 'Application submitted. You will be notified once approved.',
            'status'  => $affiliate->status,
        ], 201);
    }

    // GET /api/me/referral — customer's own referral info
    public function myReferral(Request $request): JsonResponse
    {
        $customer  = $request->user('customer');
        $affiliate = $customer->affiliate ?? Affiliate::where('customer_id', $customer->id)->first();

        return response()->json([
            'username'    => $customer->username,
            'referral_code' => $customer->username,
            'affiliate'   => $affiliate ? [
                'status'          => $affiliate->status,
                'commission_rate' => $affiliate->effectiveCommissionRate(),
            ] : null,
        ]);
    }
}
