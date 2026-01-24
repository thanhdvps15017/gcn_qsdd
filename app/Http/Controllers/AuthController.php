<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Form đăng nhập
    public function showLogin()
    {
        return view('auth.login');
    }

    // Form đăng ký
    public function showRegister()
    {
        return view('auth.register');
    }

    // XỬ LÝ ĐĂNG KÝ
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username|min:4',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Đăng ký thành công');
    }

    // XỬ LÝ ĐĂNG NHẬP
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Sai tài khoản hoặc mật khẩu',
        ]);
    }

    // ĐĂNG XUẤT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
