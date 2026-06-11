<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SettingService;

class SettingController extends Controller
{
    protected $service;

    public function __construct(SettingService $service) {
        $this->service = $service;
    }

    /**
     * Hiển thị giao diện setting background login
     */
    public function editLoginBg() {
        $loginBg = $this->service->getLoginBg();
        return view('cai-dat.settings', compact('loginBg'));
    }

    /**
     * Xử lý upload background login
     */
    public function updateLoginBg(Request $request) {
        $request->validate([
            'login_bg' => 'required|image|max:2048'
        ]);

        $path = $request->file('login_bg')->store('login-bg', 'public');
        $this->service->updateLoginBg($path);

        return redirect()->route('settings.login-bg.edit')
            ->with('success', 'Cập nhật background đăng nhập thành công');
    }
}
