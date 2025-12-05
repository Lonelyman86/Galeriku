<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        // 1. Cek apakah user sudah login? (auth()->check())
        // 2. Jika sudah, cek apakah role_id-nya BUKAN 1 (Bukan Admin)
        if (!auth()->check() || auth()->user()->role_id !== 1) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}