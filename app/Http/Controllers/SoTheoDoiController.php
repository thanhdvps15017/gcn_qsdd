<?php

namespace App\Http\Controllers;

use App\Models\SoTheoDoiGroup;
use App\Models\HoSo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SoTheoDoiExport;
use App\Services\SoTheoDoiService;

class SoTheoDoiController extends Controller
{
    protected $service;

    public function __construct(SoTheoDoiService $service) {
        $this->service = $service;
    }

    public function index() {
        $groups = $this->service->getPaginatedGroups();
        return view('so-theo-doi.index', compact('groups'));
    }

    public function create() {
        return view('so-theo-doi.create');
    }

    public function store(Request $request) {
        $request->validate([
            'book_name' => 'required|string|max:255|unique:so_theo_doi_groups,book_name',
            'description'  => 'nullable|string|max:1000',
        ]);
        
        $this->service->createGroup([
            'book_name'       => $request->book_name,
            'description'     => $request->description,
            'creator_id'      => Auth::id(),
        ]);
        
        return redirect()->route('so-theo-doi.index')->with('success', 'Đã tạo sổ theo dõi mới!');
    }

    public function show(SoTheoDoiGroup $group) {
        $hoSosTrongSo = $this->service->getHoSosTrongSo($group);
        $hoSosChuaThem = $this->service->getHoSosChuaThem($group);
        return view('so-theo-doi.show', compact('group', 'hoSosTrongSo', 'hoSosChuaThem'));
    }

    public function edit(SoTheoDoiGroup $group) {
        return view('so-theo-doi.edit', compact('group'));
    }

    public function update(Request $request, SoTheoDoiGroup $group) {
        $request->validate([
            'book_name' => [ 'required', 'string', 'max:255', Rule::unique('so_theo_doi_groups', 'book_name')->ignore($group->id) ],
            'description'  => 'nullable|string|max:1000',
        ]);

        $this->service->updateGroup($group, $request->only(['book_name', 'description']));
        return redirect()->route('so-theo-doi.index', $group)->with('success', 'Đã cập nhật sổ!');
    }

    public function destroy(SoTheoDoiGroup $group) {
        $this->service->deleteGroup($group);
        return redirect()->route('so-theo-doi.index')->with('success', 'Đã xóa sổ!');
    }

    public function batchAdd(Request $request, SoTheoDoiGroup $group) {
        $request->validate([
            'ho_so_ids' => 'required|array',
            'ho_so_ids.*' => 'exists:ho_sos,id',
        ]);

        $added = $this->service->batchAddHoSo($group, $request->ho_so_ids);
        if ($added === 0) return back()->with('warning', 'Các hồ sơ đã tồn tại trong sổ');
        
        return back()->with('success', "Đã thêm $added hồ sơ");
    }

    public function batchRemove(Request $request, SoTheoDoiGroup $group) {
        $request->validate([
            'ho_so_ids' => 'required|array',
            'ho_so_ids.*' => 'exists:ho_sos,id',
        ]);

        $removed = $this->service->batchRemoveHoSo($group, $request->ho_so_ids);
        return redirect()->back()->with('success', "Đã xóa $removed hồ sơ!");
    }

    public function exportExcel(SoTheoDoiGroup $group) {
        return Excel::download(new SoTheoDoiExport($group), 'so_theo_doi_' . $group->book_code . '.xlsx');
    }

    public function searchHoSoChuaThem(Request $request, SoTheoDoiGroup $group) {
        return response()->json($this->service->searchHoSoChuaThem($group, $request->q));
    }

    public function searchHoSoTrongSo(Request $request, SoTheoDoiGroup $group) {
        return response()->json($this->service->searchHoSoTrongSo($group, $request->q));
    }

    public function saveGhiChu(Request $request, SoTheoDoiGroup $group, HoSo $hoSo) {
        $request->validate(['notes' => 'nullable|string|max:1000']);
        $success = $this->service->saveGhiChu($group, $hoSo, $request->notes);
        
        if (!$success) return response()->json(['message' => 'Hồ sơ không thuộc sổ này'], 404);
        return response()->json(['message' => 'Lưu ghi chú thành công']);
    }
}
