<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class VendorAuthController extends Controller
{
    // ── Register ───────────────────────────────────────────────────────────────

    public function registerForm()
    {
        return view('vendor.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:vendors,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'ktp'      => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'address'  => ['required', 'string'],
        ]);

        $data['ktp'] = $request->file('ktp')->store('ktp', 'public');

        $vendor = Vendor::create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        Auth::guard('vendor')->login($vendor);

        return redirect()->route('vendor.dashboard');
    }

    // ── Login ──────────────────────────────────────────────────────────────────

    public function loginForm()
    {
        return view('vendor.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('vendor')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('vendor.dashboard'));
    }

    // ── Logout ─────────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::guard('vendor')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('vendor.login');
    }
}
