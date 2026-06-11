<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\SettingService;

class AuthController extends Controller
{
    protected $authService;
    protected $settingService;

    public function __construct(AuthService $authService, SettingService $settingService) {
        $this->authService = $authService;
        $this->settingService = $settingService;
    }

    public function showLogin() {
        $loginBg = $this->settingService->getLoginBg();
        return view('auth.login', compact('loginBg'));
    }

    public function showRegister() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'username' => 'required|unique:users,username|min:4',
            'password' => 'required|min:6|confirmed',
        ]);

        $this->authService->register($request->only('username', 'password'));

        return redirect('/login')->with('success', 'Đăng ký thành công');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($this->authService->attemptLogin($credentials)) {
            $request->session()->regenerate();
            return redirect('/ho-so');
        }

        return back()->withErrors([
            'username' => 'Sai tài khoản hoặc mật khẩu',
        ]);
    }

    public function logout(Request $request) {
        $this->authService->logout($request);
        return redirect('/login');
    }
}
