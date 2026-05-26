<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsVendor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('vendor')->check()) {
            return redirect()->route('vendor.login');
        }

        return $next($request);
    }
}
