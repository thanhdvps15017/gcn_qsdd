<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoSo;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Tổng quan cơ bản
        $tongHoSo     = HoSo::count();
        $hoanThanh    = HoSo::where('trang_thai', 'hoan_thanh')->count();
        $dangXuLy     = HoSo::where('trang_thai', '!=', 'hoan_thanh')->count();

        // 2. Hạn giải quyết
        $quaHan       = HoSo::where('trang_thai', '!=', 'hoan_thanh')
            ->whereNotNull('han_giai_quyet')
            ->where('han_giai_quyet', '<', now())->count();

        $sapHetHan    = HoSo::where('trang_thai', '!=', 'hoan_thanh')
            ->whereNotNull('han_giai_quyet')
            ->whereBetween('han_giai_quyet', [now(), now()->addDays(5)])->count();

        // 3. Top 10 hồ sơ gấp nhất
        $hoSoGap = HoSo::with(['xa', 'nguoiThamTra'])
            ->where('trang_thai', '!=', 'hoan_thanh')
            ->whereNotNull('han_giai_quyet')
            ->orderBy('han_giai_quyet', 'asc')
            ->limit(12)
            ->get();

        // 4. Theo người thẩm tra (top 10)
        $topNguoiThamTra = HoSo::where('trang_thai', '!=', 'hoan_thanh')
            ->whereNotNull('nguoi_tham_tra_id')
            ->select('nguoi_tham_tra_id', DB::raw('count(*) as tong'))
            ->with('nguoiThamTra:id,name')
            ->groupBy('nguoi_tham_tra_id')
            ->orderByDesc('tong')
            ->limit(10)
            ->get();

        // 5. Theo xã
        $theoXa = HoSo::select('xa_id', DB::raw('count(*) as tong'))
            ->with('xa:id,ten')
            ->groupBy('xa_id')
            ->orderByDesc('tong')
            ->limit(10)
            ->get();

        // 6. Theo trạng thái (đầy đủ)
        $theoTrangThai = HoSo::select('trang_thai', DB::raw('count(*) as tong'))
            ->groupBy('trang_thai')
            ->get()
            ->map(function ($item) {
                $meta = (new HoSo(['trang_thai' => $item->trang_thai]))->trang_thai_meta;
                $item->text = $meta['text'];
                $item->color = $meta['color'];
                return $item;
            });

        // 7. Xu hướng theo tháng (nếu muốn biểu đồ)
        $theoThang = HoSo::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as thang'),
            DB::raw('count(*) as tong')
        )
            ->groupBy('thang')
            ->orderBy('thang', 'desc')
            ->limit(12)
            ->get();

        return view('admin.dashboard', compact(
            'tongHoSo',
            'hoanThanh',
            'dangXuLy',
            'quaHan',
            'sapHetHan',
            'hoSoGap',
            'topNguoiThamTra',
            'theoXa',
            'theoTrangThai',
            'theoThang'
        ));
    }
}
