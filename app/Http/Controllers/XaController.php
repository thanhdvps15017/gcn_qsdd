<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Xa;
use Illuminate\Http\Request;

class XaController extends Controller
{
    public function index()
    {
        $items = Xa::orderBy('id', 'desc')->get();
        return view('xa.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:xas,name'
        ], [
            'name.required' => 'Vui lòng nhập tên xã.',
            'name.unique' => 'Tên xã này đã tồn tại trong hệ thống.'
        ]);

        Xa::create($request->only('name'));

        return back()->with('success', 'Thêm xã thành công!');
    }

    public function update(Request $request, $id)
    {
        $item = Xa::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:xas,name,' . $id
        ], [
            'name.required' => 'Vui lòng nhập tên xã.',
            'name.unique'    => 'Tên xã này đã tồn tại trong hệ thống.'
        ]);

        $item->update($request->only('name'));

        return back()->with('success', 'Cập nhật xã thành công!')
            ->with('editing_id', $id);
    }

    public function destroy($id)
    {
        Xa::findOrFail($id)->delete();
        return back()->with('success', 'Xóa xã thành công!');
    }
}
