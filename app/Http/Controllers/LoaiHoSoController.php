<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LoaiHoSoService;

class LoaiHoSoController extends Controller
{
    protected $loaiHoSoService;

    public function __construct(LoaiHoSoService $loaiHoSoService)
    {
        $this->loaiHoSoService = $loaiHoSoService;
    }

    public function index()
    {
        $items = $this->loaiHoSoService->getAllLoaiHoSos();
        return view('cai-dat.loai-ho-so.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:loai_ho_sos,name',
        ]);

        $this->loaiHoSoService->createLoaiHoSo(['name' => $request->name]);

        return redirect()->route('settings.loai-ho-so.index')
            ->with('success', 'Thêm loại hồ sơ thành công');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:loai_ho_sos,name,' . $id,
        ]);

        $this->loaiHoSoService->updateLoaiHoSo($id, ['name' => $request->name]);

        return redirect()->route('settings.loai-ho-so.index')
            ->with('success', 'Cập nhật loại hồ sơ thành công');
    }

    public function destroy($id)
    {
        $this->loaiHoSoService->deleteLoaiHoSo($id);

        return redirect()->route('settings.loai-ho-so.index')
            ->with('success', 'Đã xoá loại hồ sơ');
    }
}
