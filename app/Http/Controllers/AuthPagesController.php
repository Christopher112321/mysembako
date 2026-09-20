<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

class AuthPagesController extends BaseController
{
    public function showLogin()
    {
        // Jika sudah login, redirect berdasarkan role
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role_id == 1) {
                return redirect()->route('products.index');
            } elseif ($user->role_id == 2) {
                return redirect()->route('home');
            } elseif ($user->role_id == 3) {
                return redirect()->route('admin.dashboard');
            }
        }
        return view('auth.login');
    }

    public function submitLogin(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Coba login dengan email dan password
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect berdasarkan role
            if ($user->role_id == 1) {
                // Owner (pemilik toko)
                return redirect()->route('products.index')->with('success', 'Selamat datang, ' . $user->name . '!');
            } elseif ($user->role_id == 2) {
                // Buyer (pembeli) - redirect ke homepage
                return redirect()->route('home')->with('success', 'Selamat datang, ' . $user->name . '!');
            } elseif ($user->role_id == 3) {
                // Admin (pengelola web)
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, ' . $user->name . '!');
            }
            
            // Default redirect jika role tidak dikenali
            return redirect()->route('home');
        }

        // Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai, atau akun tidak terdaftar.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        // Jika sudah login, redirect berdasarkan role
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role_id == 1) {
                return redirect()->route('products.index');
            } elseif ($user->role_id == 2) {
                return redirect()->route('home');
            } elseif ($user->role_id == 3) {
                return redirect()->route('admin.dashboard');
            }
        }
        return view('auth.register');
    }

    public function submitRegister(Request $request)
    {
        // Validasi data input user
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        // Cari role 'buyer' (pembeli)
        $buyerRole = Role::where('name', 'buyer')->first();
        if (!$buyerRole) {
            return back()->withErrors(['error' => 'Role buyer tidak ditemukan. Silakan jalankan seeder terlebih dahulu.'])->withInput();
        }

        // Masukkan data ke database
        $user = new \App\Models\User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->phone = isset($validated['phone']) ? $validated['phone'] : null;
        $user->role_id = $buyerRole->id; // Buyer role (id=2)
        $user->save();

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
    }

    public function showRegisterAdmin()
    {
        // Jika sudah login, redirect berdasarkan role
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role_id == 1) {
                return redirect()->route('products.index');
            } elseif ($user->role_id == 2) {
                return redirect()->route('home');
            } elseif ($user->role_id == 3) {
                return redirect()->route('admin.dashboard');
            }
        }
        return view('auth.register-admin');
    }

    public function submitRegisterAdmin(Request $request)
    {
        // Validasi data input owner (pemilik toko)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'no_telp' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
        ]);

        // Cari role 'owner' (pemilik toko)
        $ownerRole = Role::where('name', 'owner')->first();
        if (!$ownerRole) {
            return back()->withErrors(['error' => 'Role owner tidak ditemukan. Silakan jalankan seeder terlebih dahulu.'])->withInput();
        }

        // Masukkan data ke database dengan role owner
        $user = new \App\Models\User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->phone = isset($validated['no_telp']) ? $validated['no_telp'] : null;
        $user->location = isset($validated['location']) ? $validated['location'] : null;
        $user->role_id = $ownerRole->id; // Owner role (id=1)
        $user->save();

        // Auto login setelah register
        Auth::login($user);

        return redirect()->route('products.index')->with('success', 'Selamat! Toko Anda berhasil dibuat. Silakan mulai menambahkan produk.');
    }

    public function index()
    {
        return view('index');
    }
}
