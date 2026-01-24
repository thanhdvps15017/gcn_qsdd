<?php

namespace App\Http\Controllers;

use App\Models\LoaiThuTuc;
use Illuminate\Http\Request;

class LoaiThuTucController extends Controller
{
    public function index()
    {
        $items = LoaiThuTuc::orderBy('id', 'desc')->get();
        return view('loai-thu-tuc.index', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|unique:loai_thu_tucs,name',
            'ngay_tra_ket_qua' => 'nullable|integer|min:1|max:365'
        ], [
            'name.required'    => 'Vui lòng nhập tên thủ tục.',
            'name.unique'      => 'Tên thủ tục này đã tồn tại.',
            'ngay_tra_ket_qua.integer' => 'Số ngày phải là số nguyên.',
            'ngay_tra_ket_qua.min'     => 'Số ngày tối thiểu là 1.',
            'ngay_tra_ket_qua.max'     => 'Số ngày tối đa là 365.'
        ]);

        LoaiThuTuc::create($validated);

        return back()->with('success', 'Thêm loại thủ tục thành công!');
    }

    public function update(Request $request, $id)
    {
        $item = LoaiThuTuc::findOrFail($id);

        $validated = $request->validate([
            'name'             => 'required|unique:loai_thu_tucs,name,' . $id,
            'ngay_tra_ket_qua' => 'nullable|integer|min:1|max:365'
        ], [
            'name.required' => 'Vui lòng nhập tên thủ tục.',
            'name.unique'   => 'Tên thủ tục này đã tồn tại.',
        ]);

        $item->update($validated);

        return back()->with('success', 'Cập nhật loại thủ tục thành công!');
    }

    public function destroy($id)
    {
        LoaiThuTuc::findOrFail($id)->delete();
        return back()->with('success', 'Xóa loại thủ tục thành công!');
    }
}