<?php

namespace App\Http\Controllers\XuatFile;

use App\Http\Controllers\Controller;
use App\Models\LoaiHoSo;
use App\Models\LoaiThuTuc;
use App\Models\Xa;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\HoSoExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\XuatExcelService;

class XuatExcelController extends Controller
{
    protected $service;

    public function __construct(XuatExcelService $service) {
        $this->service = $service;
    }

    public function index(Request $request) {
        $hoSos = $this->service->getHoSosForIndex($request->all());
        
        $loaiHoSos   = LoaiHoSo::all();
        $loaiThuTucs = LoaiThuTuc::all();
        $xas         = Xa::all();
        $users       = User::all();

        return view('xuat-file.excel', compact('hoSos', 'loaiHoSos', 'loaiThuTucs', 'xas', 'users'));
    }

    public function export(Request $request) {
        $hoSos = $this->service->getHoSosForExport($request->all());
        return Excel::download(new HoSoExport($hoSos), 'bao_cao_ho_so.xlsx');
    }
}
