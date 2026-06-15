<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect('/admin');
        }

        if (! auth()->user()->can('send_broadcast')) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
