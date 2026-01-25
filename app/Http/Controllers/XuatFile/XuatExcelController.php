<?php

namespace App\Http\Controllers\XuatFile;

use App\Http\Controllers\Controller;
use App\Models\HoSo;
use App\Models\LoaiHoSo;
use App\Models\LoaiThuTuc;
use App\Models\Xa;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\HoSoExport;
use Maatwebsite\Excel\Facades\Excel;

class XuatExcelController extends Controller
{
    public function index(Request $request)
    {
        $query = HoSo::query()
            ->with(['loaiHoSo', 'loaiThuTuc', 'xa', 'nguoiThamTra']);

        // ===== FILTER =====
        if ($request->loai_ho_so_id) {
            $query->where('loai_ho_so_id', $request->loai_ho_so_id);
        }

        if ($request->loai_thu_tuc_id) {
            $query->where('loai_thu_tuc_id', $request->loai_thu_tuc_id);
        }

        if ($request->xa_id) {
            $query->where('xa_id', $request->xa_id);
        }

        if ($request->nguoi_tham_tra_id) {
            $query->where('nguoi_tham_tra_id', $request->nguoi_tham_tra_id);
        }

        if ($request->created_from) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }

        if ($request->created_to) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        $hoSos = $query->latest()->paginate(20)->withQueryString();

        // dữ liệu cho select box
        $loaiHoSos   = LoaiHoSo::all();
        $loaiThuTucs = LoaiThuTuc::all();
        $xas         = Xa::all();
        $users       = User::all();

        return view(
            'xuat-file.excel.index',
            compact('hoSos', 'loaiHoSos', 'loaiThuTucs', 'xas', 'users')
        );
    }

    public function export(Request $request)
    {
        $query = HoSo::query()
            ->with(['loaiHoSo', 'loaiThuTuc', 'xa', 'nguoiThamTra']);

        // ===== APPLY FILTER Y HỆT =====
        foreach (
            [
                'loai_ho_so_id',
                'loai_thu_tuc_id',
                'xa_id',
                'nguoi_tham_tra_id',
            ] as $field
        ) {
            if ($request->$field) {
                $query->where($field, $request->$field);
            }
        }

        if ($request->created_from) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }

        if ($request->created_to) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        return Excel::download(
            new HoSoExport($query->get()),
            'bao_cao_ho_so.xlsx'
        );
    }
}
