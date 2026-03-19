<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminOrStockRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !in_array((string) Auth::user()->role, ['Admin', 'Stock'], true)) {
            return redirect()->route('admin.production-reports.index')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
