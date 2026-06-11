<?php

namespace App\Repositories;

use App\Models\HoSo;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function getTongHoSo() { return HoSo::count(); }
    public function getHoanThanh() { return HoSo::where('status', 'hoan_thanh')->count(); }
    public function getDangXuLy() { return HoSo::where('status', '!=', 'hoan_thanh')->count(); }
    public function getQuaHan() { return HoSo::where('status', '!=', 'hoan_thanh')->whereNotNull('deadline')->where('deadline', '<', now())->count(); }
    public function getSapHetHan() { return HoSo::where('status', '!=', 'hoan_thanh')->whereNotNull('deadline')->whereBetween('deadline', [now(), now()->addDays(5)])->count(); }
    public function getHoSoGap() { return HoSo::with(['xa', 'nguoiThamTra'])->where('status', '!=', 'hoan_thanh')->whereNotNull('deadline')->orderBy('deadline', 'asc')->limit(12)->get(); }
    public function getTopNguoiThamTra() { return HoSo::where('status', '!=', 'hoan_thanh')->whereNotNull('inspector_id')->select('inspector_id', DB::raw('count(*) as tong'))->with('nguoiThamTra:id,name')->groupBy('inspector_id')->orderByDesc('tong')->limit(10)->get(); }
    public function getTheoXa() { return HoSo::select('ward_id', DB::raw('count(*) as tong'))->with('xa:id,name')->groupBy('ward_id')->orderByDesc('tong')->limit(10)->get(); }
    public function getTheoTrangThai() { return HoSo::select('status', DB::raw('count(*) as tong'))->groupBy('status')->get(); }
    public function getTheoThang() { return HoSo::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as thang'), DB::raw('count(*) as tong'))->groupBy('thang')->orderBy('thang', 'desc')->limit(12)->get(); }
}
