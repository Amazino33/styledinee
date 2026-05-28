<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCustomerHasUsername
{
    public function handle(Request $request, Closure $next)
    {
        $customer = Auth::guard('customer_web')->user();

        if ($customer && $customer->needsUsername() && ! $request->routeIs('account.username')) {
            return redirect()->route('account.username');
        }

        return $next($request);
    }
}
