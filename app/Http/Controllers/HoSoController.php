<?php

namespace App\Http\Controllers;

use App\Models\HoSo;
use App\Models\HoSoFile;
use App\Models\LoaiHoSo;
use App\Models\LoaiThuTuc;
use App\Models\Xa;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class HoSoController extends Controller
{
    public function index(Request $request)
    {
        $query = HoSo::query();

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
        $query->orderBy('created_at', $sort);

        /* 📄 Lấy dữ liệu */
        $hoSos = $query
            ->with(['loaiHoSo', 'loaiThuTuc', 'xa', 'nguoiThamTra'])
            ->paginate($request->get('per_page', 20))
            ->withQueryString();

        return view('ho-so.index', [
            'hoSos'       => $hoSos,
            'loaiHoSos'   => LoaiHoSo::all(),
            'loaiThuTucs' => LoaiThuTuc::all(),
            'xas'         => Xa::all(),
            'users'       => User::all(),
        ]);
    }

    public function create()
    {
        return view('ho-so.create', [
            'loaiHoSos' => LoaiHoSo::all(),
            'loaiThuTucs' => LoaiThuTuc::all(),
            'xas' => Xa::all(),
            'users' => User::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ma_ho_so'            => 'required|unique:ho_sos,ma_ho_so',
            'xung_ho'             => 'nullable|string',
            'ten_chu_ho_so'       => 'nullable|string',
            'sdt_chu_ho_so'       => 'nullable|string',

            'loai_ho_so_id'       => 'required|exists:loai_ho_sos,id',
            'loai_thu_tuc_id'     => 'required|exists:loai_thu_tucs,id',
            'xa_id'               => 'required|exists:xas,id',
            'nguoi_tham_tra_id'   => 'required|exists:users,id',

            'chu_su_dung'         => 'nullable|array',
            'chu_su_dung.*'       => 'nullable|string',

            'uy_quyen'            => 'nullable|array',
            'uy_quyen.*'          => 'nullable|string',

            'thua_chung'          => 'nullable|array',
            'thua_chung.*.to'     => 'nullable|string',
            'thua_chung.*.thua'   => 'nullable|string',
            'thua_chung.*.dien_tich' => 'nullable|numeric',

            'ngay_cap_gcn'        => 'nullable|date',
            'so_vao_so'           => 'nullable|string',
            'so_phat_hanh'        => 'nullable|string',

            'thong_tin_rieng'     => 'nullable|array',
            'thong_tin_rieng.loai' => 'nullable|string|in:tachthua_chuyennhuong,capdoi,chuyennhuong,tachthua,capdoi_chuyennhuong',
            'thong_tin_rieng.data' => 'nullable|array',
            'thong_tin_rieng.data.ho_ten'       => 'nullable|string',
            'thong_tin_rieng.data.cccd'         => 'nullable|string',
            'thong_tin_rieng.data.ngay_cap_cccd' => 'nullable|date',
            'thong_tin_rieng.data.dia_chi'      => 'nullable|string',

            'thong_tin_rieng.data.thua'         => 'nullable|array',
            'thong_tin_rieng.data.thua.*.to'       => 'nullable|string',
            'thong_tin_rieng.data.thua.*.thua'     => 'nullable|string',
            'thong_tin_rieng.data.thua.*.dien_tich' => 'nullable|numeric',
            'thong_tin_rieng.data.thua.*.ghi_chu'   => 'nullable|string',

            'ghi_chu'               => 'nullable|string',
            'files'                 => 'nullable|array',
            'files.*'               => 'file|max:10240',
            'han_giai_quyet'        => 'nullable|date',
            'trang_thai'            => 'nullable|string',
        ]);

        // Chuẩn hóa thua_chung
        $thuaChung = $request->input('thua_chung', []);
        if (is_array($thuaChung) && !empty($thuaChung)) {
            $data['thua_chung'] = $this->normalizeIndexedRows($thuaChung);
        }

        // Chuẩn hóa thong_tin_rieng
        $rieng = $request->input('thong_tin_rieng', ['loai' => null, 'data' => []]);
        $riengData = $rieng['data'] ?? [];

        // Chuẩn hóa mảng thua trong thông tin riêng
        $thuaRieng = $riengData['thua'] ?? [];
        if (is_array($thuaRieng) && !empty($thuaRieng)) {
            $normalizedRieng = $this->normalizeIndexedRows($thuaRieng);

            // Đảm bảo mỗi thửa có đầy đủ key mặc định
            foreach ($normalizedRieng as &$item) {
                $item = array_merge([
                    'to'       => '',
                    'thua'     => '',
                    'dien_tich' => null,
                    'ghi_chu'  => '',
                ], (array) $item);
            }
            unset($item);

            $rieng['data']['thua'] = $normalizedRieng;
        }

        $data['thong_tin_rieng'] = $rieng;

        // Tính hạn giải quyết tự động nếu chưa có
        if ($request->filled('loai_thu_tuc_id') && empty($data['han_giai_quyet'])) {
            $loaiThuTuc = LoaiThuTuc::find($request->loai_thu_tuc_id);
            if ($loaiThuTuc && $loaiThuTuc->ngay_tra_ket_qua !== null) {
                $ngayTiepNhan = Carbon::today();
                $han = $ngayTiepNhan->copy()->addDays((int) $loaiThuTuc->ngay_tra_ket_qua);
                $data['han_giai_quyet'] = $han->toDateString();
            }
        }

        // Trạng thái mặc định
        $data['trang_thai'] = $data['trang_thai'] ?? 'dang_giai_quyet';

        $hoSo = HoSo::create($data);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store(
                    'ho_so_files/' . $hoSo->id,
                    'public'
                );

                $hoSo->files()->create([
                    'ten_file'   => $file->getClientOriginalName(),
                    'duong_dan'  => $path,
                    'loai_file'  => $file->getClientOriginalExtension(),
                    'kich_thuoc' => $file->getSize(),
                ]);
            }
        }


        return redirect()->route('ho-so.index')->with('success', 'Đã lưu hồ sơ thành công!');
    }

    public function show(HoSo $hoSo)
    {
        $hoSo->load(['loaiHoSo', 'loaiThuTuc', 'xa', 'nguoiThamTra', 'files', 'trangThaiLogs.user']);
        return view('ho-so.show', compact('hoSo'));
    }

    public function edit(HoSo $hoSo)
    {
        return view('ho-so.edit', [
            'hoSo' => $hoSo,
            'loaiHoSos' => LoaiHoSo::all(),
            'loaiThuTucs' => LoaiThuTuc::all(),
            'xas' => Xa::all(),
            'users' => User::all(),
        ]);
    }

    public function update(Request $request, HoSo $hoSo)
    {
        $data = $request->validate([
            'ma_ho_so'            => 'required|unique:ho_sos,ma_ho_so,' . $hoSo->id,
            'xung_ho'             => 'nullable|string',
            'ten_chu_ho_so'       => 'nullable|string',
            'sdt_chu_ho_so'       => 'nullable|string',

            'loai_ho_so_id'       => 'required|exists:loai_ho_sos,id',
            'loai_thu_tuc_id'     => 'required|exists:loai_thu_tucs,id',
            'xa_id'               => 'required|exists:xas,id',
            'nguoi_tham_tra_id'   => 'required|exists:users,id',

            'chu_su_dung'         => 'nullable|array',
            'chu_su_dung.*'       => 'nullable|string',

            'uy_quyen'            => 'nullable|array',
            'uy_quyen.*'          => 'nullable|string',

            'thua_chung'          => 'nullable|array',
            'thua_chung.*.to'     => 'nullable|string',
            'thua_chung.*.thua'   => 'nullable|string',
            'thua_chung.*.dien_tich' => 'nullable|numeric',

            'ngay_cap_gcn'        => 'nullable|date',
            'so_vao_so'           => 'nullable|string',
            'so_phat_hanh'        => 'nullable|string',

            'thong_tin_rieng'     => 'nullable|array',
            'thong_tin_rieng.loai' => 'nullable|string|in:tachthua_chuyennhuong,capdoi,chuyennhuong,tachthua,capdoi_chuyennhuong',
            'thong_tin_rieng.data' => 'nullable|array',
            'thong_tin_rieng.data.ho_ten'       => 'nullable|string',
            'thong_tin_rieng.data.cccd'         => 'nullable|string',
            'thong_tin_rieng.data.ngay_cap_cccd' => 'nullable|date',
            'thong_tin_rieng.data.dia_chi'      => 'nullable|string',

            'thong_tin_rieng.data.thua'         => 'nullable|array',
            'thong_tin_rieng.data.thua.*.to'       => 'nullable|string',
            'thong_tin_rieng.data.thua.*.thua'     => 'nullable|string',
            'thong_tin_rieng.data.thua.*.dien_tich' => 'nullable|numeric',
            'thong_tin_rieng.data.thua.*.ghi_chu'   => 'nullable|string',

            'ghi_chu'             => 'nullable|string',
            'han_giai_quyet'      => 'nullable|date',
            'trang_thai'          => 'nullable|string',
        ]);

        // Chuẩn hóa thua_chung
        $thuaChung = $request->input('thua_chung', []);
        if (is_array($thuaChung) && !empty($thuaChung)) {
            $data['thua_chung'] = $this->normalizeIndexedRows($thuaChung);
        }

        // Chuẩn hóa thong_tin_rieng
        $rieng = $request->input('thong_tin_rieng', ['loai' => null, 'data' => []]);
        $riengData = $rieng['data'] ?? [];

        $thuaRieng = $riengData['thua'] ?? [];
        if (is_array($thuaRieng) && !empty($thuaRieng)) {
            $normalizedRieng = $this->normalizeIndexedRows($thuaRieng);

            foreach ($normalizedRieng as &$item) {
                $item = array_merge([
                    'to'       => '',
                    'thua'     => '',
                    'dien_tich' => null,
                    'ghi_chu'  => '',
                ], (array) $item);
            }
            unset($item);

            $rieng['data']['thua'] = $normalizedRieng;
        }

        $data['thong_tin_rieng'] = $rieng;

        // Cập nhật hạn giải quyết nếu thay đổi loại thủ tục
        if ($request->filled('loai_thu_tuc_id') && empty($data['han_giai_quyet'])) {
            $loaiThuTuc = LoaiThuTuc::find($request->loai_thu_tuc_id);
            if ($loaiThuTuc && $loaiThuTuc->ngay_tra_ket_qua !== null) {
                $ngayTiepNhan = Carbon::today();
                $han = $ngayTiepNhan->copy()->addDays((int) $loaiThuTuc->ngay_tra_ket_qua);
                $data['han_giai_quyet'] = $han->toDateString();
            }
        }

        // Trạng thái: nếu không gửi thì giữ nguyên cũ
        if (empty($data['trang_thai'])) {
            $data['trang_thai'] = $hoSo->trang_thai ?? 'dang_giai_quyet';
        }

        $hoSo->update($data);

        return redirect()->route('ho-so.show', $hoSo)->with('success', 'Đã cập nhật hồ sơ thành công!');
    }

    public function destroy(HoSo $hoSo)
    {
        $hoSo->delete();
        return redirect()->route('ho-so.index')->with('success', 'Đã xóa hồ sơ');
    }

    public function destroyFile(HoSo $hoSo, HoSoFile $hoSoFile)
    {
        if ($hoSoFile->ho_so_id !== $hoSo->id) {
            abort(403);
        }

        Storage::disk('public')->delete($hoSoFile->duong_dan);
        $hoSoFile->delete();

        return response()->json(['success' => true]);
    }

    public function updateTrangThai(Request $request, HoSo $hoSo)
    {
        $request->validate([
            'trang_thai' => 'required|string',
        ]);

        $hoSo->update([
            'trang_thai' => $request->trang_thai,
        ]);

        return response()->json(['success' => true]);
    }

    private function normalizeIndexedRows(array $raw): array
    {
        // Nếu đã là mảng các row đầy đủ → trả về array_values
        if (!empty($raw) && is_array(reset($raw)) && count(reset($raw)) > 1) {
            return array_values($raw);
        }

        // Trường hợp gửi dạng rời rạc (ít xảy ra hơn với form hiện tại)
        $keys = [];
        foreach ($raw as $item) {
            if (!is_array($item)) continue;
            $k = array_keys($item);
            if (!empty($k)) $keys[] = $k[0];
        }
        $keys = array_values(array_unique($keys));
        if (empty($keys)) return [];

        $cols = count($keys);
        $rows = [];
        $total = count($raw);
        for ($i = 0; $i < $total; $i++) {
            $rowIndex = intdiv($i, $cols);
            $key = $keys[$i % $cols];
            $rows[$rowIndex][$key] = $raw[$i][$key] ?? null;
        }

        // Lọc bỏ row rỗng
        return array_values(array_filter($rows, function ($row) {
            return is_array($row) && !empty(array_filter($row));
        }));
    }

    public function saveGhiChu(Request $request, HoSo $hoSo)
    {
        $request->validate([
            'ghi_chu' => 'nullable|string|max:1000',
        ]);

        $hoSo->update([
            'ghi_chu' => $request->ghi_chu,
        ]);

        return response()->json([
            'message' => 'Lưu ghi chú thành công',
            'ghi_chu'  => $hoSo->fresh()->ghi_chu,
        ]);
    }
}
