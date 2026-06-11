<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\XaService;

class XaController extends Controller
{
    protected $xaService;

    public function __construct(XaService $xaService)
    {
        $this->xaService = $xaService;
    }

    public function index()
    {
        $items = $this->xaService->getAllXas();
        return view('cai-dat.xa-phuong.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:xas,name'
        ], [
            'name.required' => 'Vui lòng nhập tên xã.',
            'name.unique' => 'Tên xã này đã tồn tại trong hệ thống.'
        ]);

        $this->xaService->createXa($request->only('name'));

        return back()->with('success', 'Thêm xã thành công!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:xas,name,' . $id
        ], [
            'name.required' => 'Vui lòng nhập tên xã.',
            'name.unique'   => 'Tên xã này đã tồn tại trong hệ thống.'
        ]);

        $this->xaService->updateXa($id, $request->only('name'));

        return back()->with('success', 'Cập nhật xã thành công!')
            ->with('editing_id', $id);
    }

    public function destroy($id)
    {
        $this->xaService->deleteXa($id);
        return back()->with('success', 'Xóa xã thành công!');
    }
}
