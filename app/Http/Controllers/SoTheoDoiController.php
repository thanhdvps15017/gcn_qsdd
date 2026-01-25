<?php

namespace App\Http\Controllers;

use App\Models\SoTheoDoiGroup;
use App\Models\HoSo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SoTheoDoiExport;

class SoTheoDoiController extends Controller
{
    public function index()
    {
        $groups = SoTheoDoiGroup::withCount('hoSos')
            ->with('nguoiTao')
            ->latest()
            ->paginate(15);

        return view('so-theo-doi.index', compact('groups'));
    }

    public function create()
    {
        return view('so-theo-doi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_so' => 'required|string|max:255|unique:so_theo_doi_groups,ten_so',
            'mo_ta'  => 'nullable|string|max:1000',
        ]);

        SoTheoDoiGroup::create([
            'ten_so'       => $request->ten_so,
            'mo_ta'        => $request->mo_ta,
            'nguoi_tao_id' => Auth::id(),
        ]);

        return redirect()->route('so-theo-doi.index')
            ->with('success', 'Đã tạo sổ theo dõi mới!');
    }

    public function show(SoTheoDoiGroup $group)
    {
        $hoSosTrongSo = $group->hoSos()
            ->with(['loaiHoSo', 'loaiThuTuc', 'xa'])
            ->paginate(20);

        $hoSosChuaThem = HoSo::whereNotIn('id', $group->hoSos->pluck('id'))
            ->select('id', 'ma_ho_so', 'ten_chu_ho_so')
            ->orderBy('ma_ho_so')
            ->get();

        return view('so-theo-doi.show', compact('group', 'hoSosTrongSo', 'hoSosChuaThem'));
    }

    public function edit(SoTheoDoiGroup $group)
    {
        return view('so-theo-doi.edit', compact('group'));
    }

    public function update(Request $request, SoTheoDoiGroup $group)
    {
        $request->validate([
            'ten_so' => [
                'required',
                'string',
                'max:255',
                Rule::unique('so_theo_doi_groups', 'ten_so')->ignore($group->id),
            ],
            'mo_ta'  => 'nullable|string|max:1000',
        ]);

        $group->update($request->only(['ten_so', 'mo_ta']));

        return redirect()->route('so-theo-doi.index', $group)
            ->with('success', 'Đã cập nhật sổ!');
    }

    public function destroy(SoTheoDoiGroup $group)
    {
        $group->delete();

        return redirect()->route('so-theo-doi.index')
            ->with('success', 'Đã xóa sổ!');
    }

    public function batchAdd(Request $request, SoTheoDoiGroup $group)
    {
        $request->validate([
            'ho_so_ids' => 'required|array',
            'ho_so_ids.*' => 'exists:ho_sos,id',
        ]);

        $count = count($request->ho_so_ids);
        $group->hoSos()->syncWithoutDetaching($request->ho_so_ids);

        return redirect()->back()->with('success', "Đã thêm $count hồ sơ!");
    }

    public function batchRemove(Request $request, SoTheoDoiGroup $group)
    {
        $request->validate([
            'ho_so_ids' => 'required|array',
            'ho_so_ids.*' => 'exists:ho_sos,id',
        ]);

        $count = count($request->ho_so_ids);
        $group->hoSos()->detach($request->ho_so_ids);

        return redirect()->back()->with('success', "Đã xóa $count hồ sơ!");
    }

    public function exportExcel(SoTheoDoiGroup $group)
    {
        return Excel::download(new SoTheoDoiExport($group), 'so_theo_doi_' . $group->ma_so . '.xlsx');
    }

    public function searchHoSoChuaThem(Request $request, SoTheoDoiGroup $group)
    {
        $keyword = $request->q;

        $ids = $group->hoSos()->pluck('ho_sos.id');

        $data = HoSo::whereNotIn('id', $ids)
            ->when($keyword, function ($q) use ($keyword) {
                $q->where('ma_ho_so', 'like', "%{$keyword}%")
                    ->orWhere('ten_chu_ho_so', 'like', "%{$keyword}%");
            })
            ->orderBy('ma_ho_so')
            ->limit(50)
            ->get(['id', 'ma_ho_so', 'ten_chu_ho_so']);

        return response()->json($data);
    }

    public function searchHoSoTrongSo(Request $request, SoTheoDoiGroup $group)
    {
        $keyword = $request->q;

        $data = $group->hoSos()
            ->when($keyword, function ($q) use ($keyword) {
                $q->where('ma_ho_so', 'like', "%{$keyword}%")
                    ->orWhere('ten_chu_ho_so', 'like', "%{$keyword}%");
            })
            ->with('chuSuDung:id,ho_ten')
            ->limit(50)
            ->get();

        return response()->json($data);
    }
}
