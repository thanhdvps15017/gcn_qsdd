<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoSo;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Tổng quan
        $tongHoSo    = HoSo::count();
        $hoanThanh   = HoSo::where('trang_thai', 'hoan_thanh')->count();

        // Sắp hết hạn (≤ 5 ngày) & chưa hoàn thành
        $sapHetHan = HoSo::where('trang_thai', '!=', 'hoan_thanh')
            ->whereNotNull('han_giai_quyet')
            ->where('han_giai_quyet', '<=', now()->addDays(5))
            ->where('han_giai_quyet', '>=', now())
            ->count();

        // Quá hạn
        $quaHan = HoSo::where('trang_thai', '!=', 'hoan_thanh')
            ->whereNotNull('han_giai_quyet')
            ->where('han_giai_quyet', '<', now())
            ->count();

        // Danh sách hồ sơ cần chú ý (sắp hết hạn + quá hạn)
        $hoSoGap = HoSo::with(['xa'])
            ->where('trang_thai', '!=', 'hoan_thanh')
            ->whereNotNull('han_giai_quyet')
            ->where('han_giai_quyet', '<=', now()->addDays(5))
            ->orderBy('han_giai_quyet', 'asc')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'tongHoSo',
            'hoanThanh',
            'sapHetHan',
            'quaHan',
            'hoSoGap'
        ));
    }
}
