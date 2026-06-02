<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // 2. Gunakan Auth::attempt untuk mengecek dan membuat sesi login yang aman
        if (Auth::attempt($credentials)) {

            // 3. Wajib: Buat ulang ID sesi untuk mencegah pencurian sesi (Session Fixation)
            $request->session()->regenerate();

            // 4. Ambil data user yang sedang login
            $user = Auth::user();

            // 5. Alihkan berdasarkan peran (role)
            if ($user->role == 1) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role == 2) {
                return redirect()->intended('/kasir');
            } else {
                return redirect()->intended('/dapur');
            }
        }

        // Jika username atau password salah
        return back()->with('error', 'Username atau password salah ❌');
    }

    public function logout(Request $request)
    {
        // 1. Keluarkan pengguna
        Auth::logout();

        // 2. Hancurkan seluruh data sesi saat ini
        $request->session()->invalidate();

        // 3. Buat ulang token CSRF agar token lama tidak bisa disalahgunakan
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Berhasil logout ✅');
    }
}
