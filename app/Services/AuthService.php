<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data) {
        return User::create([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function attemptLogin(array $credentials) {
        return Auth::attempt($credentials);
    }

    public function logout($request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
