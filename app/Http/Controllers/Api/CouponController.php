<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    // GET /api/coupons/validate/{code}?order_total=5000
    public function validate(Request $request, string $code): JsonResponse
    {
        $request->validate([
            'order_total' => ['required', 'numeric', 'min:0'],
        ]);

        $customer = $request->user('customer');
        $result   = app(CouponService::class)->validate($code, (float) $request->order_total, $customer);

        $status = $result['valid'] ? 200 : 422;

        return response()->json([
            'valid'    => $result['valid'],
            'discount' => $result['discount'],
            'message'  => $result['message'],
        ], $status);
    }
}
