<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // ✅ Wajib ditambahkan agar Auth bisa dibaca

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan pengguna sudah login terlebih dahulu
        if (!Auth::check()) {
            return redirect('/');
        }

        // 2. Ambil data user yang sedang login
        $user = Auth::user();

        // 3. Cek apakah role pengguna ada di dalam daftar yang diizinkan (parameter ...$roles)
        if (in_array($user->role, $roles)) {
            return $next($request); // Izinkan masuk
        }

        // 4. Jika role tidak sesuai, kembalikan ke halaman sebelumnya
        return back()->with('error', 'Akses ditolak! Anda tidak memiliki izin ke halaman ini ⛔');
    }
}
