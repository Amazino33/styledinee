<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::guard('customer_web')->check()) {
            return redirect()->route('account.login')->with('intended', $request->url());
        }

        return $next($request);
    }
}
