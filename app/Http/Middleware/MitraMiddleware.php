<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class MitraMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('mitra')->check()) {
            Alert::warning('Akses Ditolak', 'Silahkan login terlebih dahulu');
            return redirect()->route('root');
        }

        if (Auth::guard('mitra')->user()->status !== 'active') {
            Auth::guard('mitra')->logout();
            Alert::error('Akses Ditolak', 'Akun Anda tidak aktif. Silahkan hubungi admin.');
            return redirect()->route('root');
        }

        return $next($request);
    }
}
