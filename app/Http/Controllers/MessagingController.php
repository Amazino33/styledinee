<?php

namespace App\Http\Controllers;

use App\Jobs\SendBroadcastJob;
use App\Models\AppSetting;
use App\Models\BroadcastLog;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessagingController extends Controller
{
    public function index()
    {
        $logs = BroadcastLog::with('sender')
            ->latest()
            ->take(20)
            ->get();

        return view('messaging.index', compact('logs'));
    }

    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'message'       => 'required|string|max:1600',
            'audience_type' => 'required|string',
            'recipients'    => 'required|string',
        ]);

        $phones = $this->parseRecipients($request->recipients);

        if (empty($phones)) {
            return response()->json(['error' => 'No valid recipients found.'], 422);
        }

        $log = BroadcastLog::create([
            'created_by'        => auth()->id(),
            'audience_type'     => $request->audience_type,
            'message'           => $request->message,
            'recipient_count'   => count($phones),
        ]);

        $delay = max(1, (int) AppSetting::get('broadcast_delay_seconds', 3));

        foreach ($phones as $index => $phone) {
            SendBroadcastJob::dispatch($log->id, $phone, $request->message)
                ->delay(now()->addSeconds($index * $delay));
        }

        return response()->json([
            'message' => "Queued for {$log->recipient_count} recipient(s).",
        ]);
    }

    private function parseRecipients(string $raw): array
    {
        return collect(explode(',', $raw))
            ->map(fn ($r) => trim($r))
            ->filter()
            ->map(fn ($r) => Customer::normalizePhone($r))
            ->unique()
            ->values()
            ->all();
    }

    public function audiencePhones(Request $request): JsonResponse
    {
        $request->validate(['type' => 'required|string']);

        $phones = match ($request->type) {
            'all_customers' => Customer::whereNotNull('phone')->pluck('phone')->all(),
            'new_customers' => Customer::whereNotNull('phone')
                                ->where('created_at', '>=', now()->subDays(30))
                                ->pluck('phone')->all(),
            default         => [],
        };

        return response()->json(['phones' => $phones]);
    }

    public function searchCustomers(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2|max:100']);

        $customers = Customer::whereNotNull('phone')
            ->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('phone', 'like', '%' . $request->q . '%');
            })
            ->limit(10)
            ->get(['name', 'phone']);

        return response()->json(['customers'  => $customers]);
    }
}
