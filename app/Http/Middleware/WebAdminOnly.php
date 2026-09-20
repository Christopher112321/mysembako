<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebAdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!\Illuminate\Support\Facades\Auth::check() || \Illuminate\Support\Facades\Auth::user()->role_id != 3) {
            abort(403, 'Anda tidak memiliki akses. Hanya pengelola web yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
