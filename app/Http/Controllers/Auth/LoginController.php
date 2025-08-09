<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';

        $credentials = [
            $loginType => $request->input('login'),
            'password' => $request->input('password')
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // ✅ LOGIKA PEMILAH PERAN SEKARANG ADA DI SINI
            $user = Auth::user();

            if ($user->isAdmin) { // Menggunakan accessor isAdmin dari Model User
                // Jika admin, arahkan ke dashboard admin
                return redirect()->intended(route('admin.dashboard'));
            }

            // Jika bukan admin (karyawan), arahkan ke halaman absensi
            return redirect()->intended(route('attendances.index'));
        }

        return back()->withErrors([
            'login' => 'Email/NIP atau Password yang Anda masukkan salah.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}