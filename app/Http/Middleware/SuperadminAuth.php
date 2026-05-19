<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SuperadminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard')->with('error', 'Hanya Super Administrator yang diperbolehkan mengakses halaman ini.');
        }

        return $next($request);
    }
}
