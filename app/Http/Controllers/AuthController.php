<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Display the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login attempt with validation and rate limiting.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'max:128'],
        ]);

        $throttleKey = Str::lower($request->input('username')) . '|login';

        // Rate limit: 5 attempts per minute per username
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return redirect()->back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Terlalu banyak percobaan. Silakan coba lagi dalam 60 detik.']);
        }

        $credentials = [
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            RateLimiter::clear($throttleKey);

            return redirect()->intended('/dashboard');
        }

        RateLimiter::hit($throttleKey, 60);

        return redirect()->back()
            ->withInput($request->only('username'))
            ->withErrors(['username' => 'Username atau password salah']);
    }

    /**
     * Log the user out and invalidate the session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
