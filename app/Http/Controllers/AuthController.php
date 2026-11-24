<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    /** Login pakai email atau nomor telepon. */
    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (! Auth::attempt(
            [$field => $data['login'], 'password' => $data['password'], 'is_active' => true],
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'login' => 'Email/nomor telepon atau kata sandi salah, atau akun dinonaktifkan.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda sudah logout.');
    }

    public function editPassword()
    {
        return view('auth.password');
    }

    public function updatePassword(\App\Http\Requests\PasswordRequest $request)
    {
        $request->user()->update(['password' => $request->validated()['password']]);

        return back()->with('success', 'Kata sandi diperbarui.');
    }
}