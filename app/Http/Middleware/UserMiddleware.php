<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class UserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('UserMiddleware dijalankan untuk user: ' . Auth::id());

        if (Auth::check() && Auth::user()?->isUser) {
            Log::info('Akses user DIIZINKAN untuk user: ' . Auth::id());
            return $next($request);
        }

        Log::warning('Akses user DITOLAK untuk user: ' . Auth::id());
        abort(403, 'AKSES DITOLAK.');
    }
}
