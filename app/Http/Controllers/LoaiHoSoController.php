<?php

namespace App\Http\Controllers;

use App\Models\LoaiHoSo;
use Illuminate\Http\Request;

class LoaiHoSoController extends Controller
{
    public function index()
    {
        $items = LoaiHoSo::orderBy('id', 'desc')->get();
        return view('loai-ho-so.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:loai_ho_sos,name',
        ]);

        LoaiHoSo::create([
            'name' => $request->name,
        ]);

        return redirect()->route('loai-ho-so.index')
            ->with('success', 'Thêm loại hồ sơ thành công');
    }

    public function update(Request $request, $id)
    {
        $item = LoaiHoSo::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:loai_ho_sos,name,' . $item->id,
        ]);

        $item->update([
            'name' => $request->name,
        ]);

        return redirect()->route('loai-ho-so.index')
            ->with('success', 'Cập nhật loại hồ sơ thành công');
    }

    public function destroy($id)
    {
        $item = LoaiHoSo::findOrFail($id);
        $item->delete();

        return redirect()->route('loai-ho-so.index')
            ->with('success', 'Đã xoá loại hồ sơ');
    }
}
