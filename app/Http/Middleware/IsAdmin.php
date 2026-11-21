<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pengecekan status login dan email khusus Administrator
        if (auth()->check() && auth()->user()->email === 'adminagi@sttcipasung.ac.id') {
            return $next($request);
        }

        // Jika pengguna bukan admin, hentikan akses dengan HTTP Status 403
        abort(403, 'Akses ditolak! Anda tidak memiliki wewenang Administrator.');
    }
}