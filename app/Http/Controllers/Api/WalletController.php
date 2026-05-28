<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReferralCreditLedger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    // GET /api/me/wallet
    public function show(Request $request): JsonResponse
    {
        $customer = $request->user('customer');

        if (! $customer->username) {
            return response()->json([
                'balance'      => 0,
                'transactions' => [],
                'note'         => 'Set a username to start earning referral credits.',
            ]);
        }

        $balance = $customer->creditBalance();

        $transactions = ReferralCreditLedger::where('owner_username', $customer->username)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn ($t) => [
                'type'        => $t->type,
                'amount'      => (float) $t->amount,
                'description' => $t->description,
                'date'        => $t->created_at->toDateTimeString(),
            ]);

        return response()->json([
            'balance'      => $balance,
            'transactions' => $transactions,
        ]);
    }
}
