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
        // 
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // 
        if (Auth::attempt($credentials)) {

            // 
            $request->session()->regenerate();

            // 
            $user = Auth::user();

            // 
            if ($user->role == 1) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role == 2) {
                return redirect()->intended('/kasir');
            } else {
                return redirect()->intended('/dapur');
            }
        }

        // 
        return back()->with('error', 'Username atau password salah ❌');
    }

    public function logout(Request $request)
    {
        // 
        Auth::logout();

        // 
        $request->session()->invalidate();

        // 
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Berhasil logout ✅');
    }
}
