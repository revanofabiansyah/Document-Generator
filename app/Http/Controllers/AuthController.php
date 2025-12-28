<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ===== USER =====

    public function showUserLogin()
    {
        return view('auth.login');
    }

    public function showUserRegister()
    {
        return view('auth.register');
    }

    public function registerUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'accept_terms' => 'required|accepted',
        ], [
            'accept_terms.required' => 'Silakan centang persetujuan terlebih dahulu',
            'accept_terms.accepted' => 'Anda harus menyetujui perjanjian pengguna dan kebijakan privasi',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
    }

    public function loginUser(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (auth()->user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // User redirect ke dashboard user (documents list) dengan user name
            return redirect()->route('documents.user.list', ['user' => auth()->user()->name])
                ->with('success', 'Berhasil login.');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // ===== ADMIN =====

    public function showAdminLogin()
    {
        return view('auth.login-admin');
    }

    public function showAdminRegister()
    {
        return view('auth.register-admin');
    }

    public function registerAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return redirect()->route('admin.login')->with('success', 'Registrasi admin berhasil! Silakan login dengan kredensial Anda.');
    }

    public function loginAdmin(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Anda bukan admin.',
            ]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Selamat datang di dashboard admin.');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
}


    // ===== LOGOUT =====

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Berhasil logout.');
    }
}
