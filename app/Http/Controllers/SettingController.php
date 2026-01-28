<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Hiển thị giao diện setting background login
     */
    public function editLoginBg()
    {
        $loginBg = Setting::getValue('login_bg');

        return view('settings', compact('loginBg'));
    }

    /**
     * Xử lý upload background login
     */
    public function updateLoginBg(Request $request)
    {
        $request->validate([
            'login_bg' => 'required|image|max:2048'
        ]);

        $path = $request->file('login_bg')
            ->store('login-bg', 'public');

        Setting::updateOrCreate(
            ['key' => 'login_bg'],
            ['value' => $path]
        );

        return redirect()
            ->route('settings.login-bg.edit')
            ->with('success', 'Cập nhật background đăng nhập thành công');
    }
}
