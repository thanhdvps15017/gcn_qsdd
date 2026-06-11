<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LoaiThuTucService;

class LoaiThuTucController extends Controller
{
    protected $service;

    public function __construct(LoaiThuTucService $service) {
        $this->service = $service;
    }

    public function index() {
        $items = $this->service->getAll();
        return view('cai-dat.loai-thu-tuc.index', compact('items'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name'             => 'required|unique:loai_thu_tucs,name',
            'processing_days' => 'nullable|integer|min:1|max:365'
        ], [
            'name.required'    => 'Vui lòng nhập tên thủ tục.',
            'name.unique'      => 'Tên thủ tục này đã tồn tại.',
            'processing_days.integer' => 'Số ngày phải là số nguyên.',
            'processing_days.min'     => 'Số ngày tối thiểu là 1.',
            'processing_days.max'     => 'Số ngày tối đa là 365.'
        ]);

        $this->service->create($validated);
        return back()->with('success', 'Thêm loại thủ tục thành công!');
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'name'             => 'required|unique:loai_thu_tucs,name,' . $id,
            'processing_days' => 'nullable|integer|min:1|max:365'
        ], [
            'name.required' => 'Vui lòng nhập tên thủ tục.',
            'name.unique'   => 'Tên thủ tục này đã tồn tại.',
        ]);

        $this->service->update($id, $validated);
        return back()->with('success', 'Cập nhật loại thủ tục thành công!');
    }

    public function destroy($id) {
        $this->service->delete($id);
        return back()->with('success', 'Xóa loại thủ tục thành công!');
    }
}