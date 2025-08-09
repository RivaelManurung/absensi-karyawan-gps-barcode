<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Tambahkan ini
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('AdminMiddleware dijalankan untuk user: ' . Auth::id());

        if (Auth::check() && Auth::user()?->isAdmin) {
            Log::info('Akses admin DIIZINKAN untuk user: ' . Auth::id());
            return $next($request);
        }

        Log::warning('Akses admin DITOLAK untuk user: ' . Auth::id());
        abort(403, 'AKSES DITOLAK.');
    }
}
