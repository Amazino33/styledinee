<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // GET /api/profile
    public function show(Request $request): JsonResponse
    {
        $customer = $request->user('customer');

        return response()->json([
            'data' => [
                'id'      => $customer->id,
                'name'    => $customer->name,
                'phone'   => $customer->phone,
                'email'   => $customer->email,
                'address' => $customer->address,
            ],
        ]);
    }

    // PUT /api/profile
    public function update(Request $request): JsonResponse
    {
        $customer = $request->user('customer');

        $validated = $request->validate([
            'name'    => ['sometimes', 'string', 'min:2', 'max:100'],
            'email'   => ['sometimes', 'nullable', 'email', 'max:150'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $customer->update($validated);

        return response()->json([
            'message' => 'Profile updated.',
            'data' => [
                'id'      => $customer->id,
                'name'    => $customer->name,
                'phone'   => $customer->phone,
                'email'   => $customer->email,
                'address' => $customer->address,
            ],
        ]);
    }

    // GET /api/profile/measurements
    public function measurements(Request $request): JsonResponse
    {
        $customer = $request->user('customer');

        $measurements = $customer->measurements()
            ->with('product:id,name')
            ->get()
            ->map(fn ($m) => [
                'product'      => $m->product?->name ?? 'General',
                'measurements' => $m->measurements,
                'unit'         => $m->unit,
                'updated_at'   => $m->updated_at->toDateString(),
            ]);

        return response()->json(['data' => $measurements]);
    }
}
