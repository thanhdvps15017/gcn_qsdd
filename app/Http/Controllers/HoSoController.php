<?php

namespace App\Http\Controllers;

use App\Models\HoSo;
use App\Models\HoSoFile;
use App\Models\LoaiHoSo;
use App\Models\LoaiThuTuc;
use App\Models\Xa;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\HoSoService;

class HoSoController extends Controller
{
    protected $service;

        /* 🔎 Tìm kiếm */
        if ($request->filled('q')) {
            $q = $request->q;

            $query->where(function ($sub) use ($q) {
                $sub->where('ma_ho_so', 'like', "%{$q}%")
                    ->orWhere('ten_chu_ho_so', 'like', "%{$q}%");
            });
        }

        /* 📌 Trạng thái */
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        /* 📂 Loại hồ sơ */
        if ($request->filled('loai_ho_so_id')) {
            $query->where('loai_ho_so_id', $request->loai_ho_so_id);
        }

        /* 📄 Loại thủ tục */
        if ($request->filled('loai_thu_tuc_id')) {
            $query->where('loai_thu_tuc_id', $request->loai_thu_tuc_id);
        }

        /* 🏘️ Xã / Phường */
        if ($request->filled('xa_id')) {
            $query->where('xa_id', $request->xa_id);
        }

        /* ⏱️ Sắp xếp */
        $sort = $request->get('sort', 'desc');
        $query->orderBy('han_giai_quyet', $sort);

        /* 📄 Lấy dữ liệu */
        $hoSos = $query
            ->with(['loaiHoSo', 'loaiThuTuc', 'xa', 'nguoiThamTra'])
            ->paginate($request->get('per_page', 20))
            ->withQueryString();

    public function index(Request $request) {
        $hoSos = $this->service->getPaginated($request->all(), $request->get('per_page', 20));
        return view('ho-so.index', [
            'hoSos'       => $hoSos,
            'loaiHoSos'   => LoaiHoSo::all(),
            'loaiThuTucs' => LoaiThuTuc::all(),
            'xas'         => Xa::all(),
            'users'       => User::all(),
        ]);
    }

    public function create() {
        return view('ho-so.create', [
            'loaiHoSos' => LoaiHoSo::all(),
            'loaiThuTucs' => LoaiThuTuc::all(),
            'xas' => Xa::all(),
            'users' => User::all(),
        ]);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'dossier_code' => 'required|unique:ho_sos,dossier_code',
            'owner_name' => 'nullable|string',
            'owner_phone' => 'nullable|string',
            'dossier_type_id' => 'required|exists:loai_ho_sos,id',
            'procedure_type_id' => 'required|exists:loai_thu_tucs,id',
            'ward_id' => 'required|exists:xas,id',
            'inspector_id' => 'required|exists:users,id',
            'land_owners' => 'nullable|array',
            'land_owners.*.salutation' => 'nullable|in:Ông,Bà',
            'land_owners.*.full_name' => 'required|string|max:255',
            'land_owners.*.date_of_birth' => 'nullable|date',
            'land_owners.*.id_card' => 'nullable|string|max:20',
            'land_owners.*.issue_date' => 'nullable|date',
            'land_owners.*.address' => 'nullable|string|max:500',
            'authorization' => 'nullable|array',
            'authorization.person' => 'nullable|string',
            'authorization.paper' => 'nullable|string',
            'shared_plots' => 'nullable|array',
            'shared_plots.*.to' => 'nullable|string',
            'shared_plots.*.thua' => 'nullable|string',
            'shared_plots.*.area' => 'nullable',
            'certificate_issue_date' => 'nullable|date',
            'registration_book_number' => 'nullable|string',
            'publication_number' => 'nullable|string',
            'address' => 'nullable|string',
            'private_info' => 'nullable|array',
            'private_info.type' => 'nullable|string|in:tachthua_chuyennhuong,capdoi,chuyennhuong,tachthua,capdoi_chuyennhuong',
            'private_info.data' => 'nullable|array',
            'private_info.data.related_person' => 'nullable|array',
            'private_info.data.related_person.*.full_name' => 'nullable|string',
            'private_info.data.related_person.*.id_card' => 'nullable|string',
            'private_info.data.thua' => 'nullable|array',
            'private_info.data.thua.*.to' => 'nullable|string',
            'private_info.data.thua.*.thua' => 'nullable|string',
            'private_info.data.thua.*.area' => 'nullable',
            'private_info.data.thua.*.notes' => 'nullable|string',
            'notes' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240',
            'deadline' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $this->service->createHoSo($data, $request->file('files'));
        return redirect()->route('ho-so.index')->with('success', 'Đã lưu hồ sơ thành công!');
    }

    public function show(HoSo $hoSo)
    {
        $hoSo->load(['loaiHoSo', 'loaiThuTuc', 'xa', 'nguoiThamTra', 'files', 'trangThaiLogs.user']);

        $thuaChung = $hoSo->thua_chung ?? [];

        $xaIds = collect($thuaChung)->pluck('xa_id')->filter()->unique();

        $xaList = \App\Models\Xa::whereIn('id', $xaIds)
            ->get()
            ->keyBy('id');

        return view('ho-so.show', compact('hoSo', 'xaList'));
    }

    public function edit($id) {
        $hoSo = $this->service->getById($id);
        return view('ho-so.edit', [
            'hoSo' => $hoSo,
            'loaiHoSos' => LoaiHoSo::all(),
            'loaiThuTucs' => LoaiThuTuc::all(),
            'xas' => Xa::all(),
            'users' => User::all(),
        ]);
    }

    public function update(Request $request, HoSo $hoSo) {
        $data = $request->validate([
            'dossier_code' => 'required|unique:ho_sos,dossier_code,' . $hoSo->id,
            'owner_name' => 'nullable|string',
            'owner_phone' => 'nullable|string',
            'dossier_type_id' => 'required|exists:loai_ho_sos,id',
            'procedure_type_id' => 'required|exists:loai_thu_tucs,id',
            'ward_id' => 'required|exists:xas,id',
            'inspector_id' => 'required|exists:users,id',
            'land_owners' => 'nullable|array',
            'land_owners.*.salutation' => 'nullable|in:Ông,Bà',
            'land_owners.*.full_name' => 'required|string|max:255',
            'land_owners.*.date_of_birth' => 'nullable|date',
            'land_owners.*.id_card' => 'nullable|string|max:20',
            'land_owners.*.issue_date' => 'nullable|date',
            'land_owners.*.address' => 'nullable|string|max:500',
            'authorization' => 'nullable|array',
            'authorization.person' => 'nullable|string',
            'authorization.paper' => 'nullable|string',
            'shared_plots' => 'nullable|array',
            'shared_plots.*.to' => 'nullable|string',
            'shared_plots.*.thua' => 'nullable|string',
            'shared_plots.*.area' => 'nullable',
            'address' => 'nullable|string|max:500',
            'certificate_issue_date' => 'nullable|date',
            'registration_book_number' => 'nullable|string',
            'publication_number' => 'nullable|string',
            'private_info' => 'nullable|array',
            'private_info.type' => 'nullable|string|in:tachthua_chuyennhuong,capdoi,chuyennhuong,tachthua,capdoi_chuyennhuong',
            'private_info.data' => 'nullable|array',
            'private_info.data.related_person' => 'nullable|array',
            'private_info.data.related_person.*.full_name' => 'nullable|string',
            'private_info.data.related_person.*.id_card' => 'nullable|string',
            'private_info.data.related_person.*.id_issue_date' => 'nullable|date',
            'private_info.data.related_person.*.address' => 'nullable|string',
            'private_info.data.thua' => 'nullable|array',
            'private_info.data.thua.*.to' => 'nullable|string',
            'private_info.data.thua.*.thua' => 'nullable|string',
            'private_info.data.thua.*.area' => 'nullable',
            'private_info.data.thua.*.notes' => 'nullable|string',
            'notes' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $this->service->updateHoSo($hoSo, $data, $request->file('files'));
        return redirect()->route('ho-so.show', $hoSo)->with('success', 'Đã cập nhật hồ sơ thành công!');
    }

    public function destroy(HoSo $hoSo) {
        $this->service->deleteHoSo($hoSo);
        return redirect()->route('ho-so.index')->with('success', 'Đã xóa hồ sơ');
    }

    public function destroyFile(HoSo $hoSo, HoSoFile $hoSoFile) {
        try {
            $this->service->deleteFile($hoSo, $hoSoFile);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            abort(403);
        }
    }

    public function updateTrangThai(Request $request, HoSo $hoSo) {
        $request->validate(['status' => 'required|string']);
        $this->service->updateTrangThai($hoSo, $request->status);
        return response()->json(['success' => true]);
    }

    public function saveGhiChu(Request $request, HoSo $hoSo) {
        $request->validate(['notes' => 'nullable|string|max:1000']);
        $this->service->saveGhiChu($hoSo, $request->notes);
        return response()->json([
            'message' => 'Lưu ghi chú thành công',
            'notes'  => $hoSo->fresh()->notes,
        ]);
    }
}
