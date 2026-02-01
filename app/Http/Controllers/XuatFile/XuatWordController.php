<?php

namespace App\Http\Controllers\XuatFile;

use App\Http\Controllers\Controller;
use App\Models\HoSo;
use App\Models\MauWord;
use App\Models\MauWordFolder;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

class XuatWordController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $hoSos = HoSo::with('xa')
            ->when($search, function ($query) use ($search) {
                $query->where('ma_ho_so', 'like', "%{$search}%")
                    ->orWhere('ten_chu_ho_so', 'like', "%{$search}%")
                    ->orWhere('sdt_chu_ho_so', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        $folders = MauWordFolder::with('mauWords')
            ->orderBy('ten')
            ->get();

        return view('xuat-file.word.index', compact('hoSos', 'folders'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'ho_so_id'    => 'required|exists:ho_sos,id',
            'mau_word_id' => 'required|exists:mau_words,id',
        ]);

        $hs = HoSo::with([
            'xa',
            'nguoiThamTra',
            'loaiHoSo',
            'loaiThuTuc',
        ])->findOrFail($request->ho_so_id);

        $mau = MauWord::findOrFail($request->mau_word_id);

        $templatePath = Storage::disk('public')->path($mau->file_path);
        if (!file_exists($templatePath)) {
            abort(404, 'Không tìm thấy file mẫu Word');
        }

        $template = new TemplateProcessor($templatePath);

        $values = [
            'id'               => $hs->id,
            'ma_ho_so'         => $hs->ma_ho_so ?? '',
            'xung_ho'          => $hs->xung_ho ?? '',
            'ten_chu_ho_so'    => $hs->ten_chu_ho_so ?? '',
            'sdt_chu_ho_so'    => $hs->sdt_chu_ho_so ?? '',
            'ngay_cap_gcn'     => optional($hs->ngay_cap_gcn)->format('d/m/Y') ?? '',
            'so_vao_so'        => $hs->so_vao_so ?? '',
            'so_phat_hanh'     => $hs->so_phat_hanh ?? '',
            'xa_ap_thon'       => $hs->xa_ap_thon ?? '',
            'ghi_chu'          => $hs->ghi_chu ?? '',
            'trang_thai'       => $hs->trang_thai ?? '',
            'han_giai_quyet'   => optional($hs->han_giai_quyet)->format('d/m/Y') ?? '',
            'created_at'       => optional($hs->created_at)->format('d/m/Y') ?? '',
            'updated_at'       => optional($hs->updated_at)->format('d/m/Y H:i') ?? '',
        ];

        $values += [
            'xa'               => optional($hs->xa)->name ?? '',
            'nguoi_tham_tra'   => optional($hs->nguoiThamTra)->name ?? '',
            'loai_ho_so'       => optional($hs->loaiHoSo)->name ?? '',
            'loai_thu_tuc'     => optional($hs->loaiThuTuc)->name ?? '',
        ];

        $chuSuDung = $hs->chu_su_dung ?? [];
        $values += [
            'chu_su_dung_ho_ten'   => $chuSuDung['ho_ten'] ?? '',
            'chu_su_dung_cccd'     => $chuSuDung['cccd'] ?? '',
            'chu_su_dung_ngay_cap' => $chuSuDung['ngay_cap'] ?? '',
            'chu_su_dung_dia_chi'  => $chuSuDung['dia_chi'] ?? '',
        ];

        $uyQuyen = $hs->uy_quyen ?? [];
        $values += [
            'uy_quyen_nguoi'    => $uyQuyen['nguoi'] ?? '',
            'uy_quyen_giay_to'  => $uyQuyen['giay'] ?? '',
        ];

        if (is_array($hs->thua_chung) && !empty($hs->thua_chung)) {
            foreach ($hs->thua_chung as $i => $thua) {
                $index = $i + 1;
                $values["thua_chung_{$index}_to"]        = $thua['to'] ?? '';
                $values["thua_chung_{$index}_thua"]      = $thua['thua'] ?? '';
                $values["thua_chung_{$index}_dien_tich"] = $thua['dien_tich'] ?? '';
            }
        }

        $thongTinRieng = $hs->thong_tin_rieng ?? [];
        $values['thong_tin_rieng_loai'] = $thongTinRieng['loai'] ?? '';

        $data = $thongTinRieng['data'] ?? [];
        $values += [
            'thong_tin_rieng_ho_ten'       => $data['ho_ten'] ?? '',
            'thong_tin_rieng_cccd'         => $data['cccd'] ?? '',
            'thong_tin_rieng_dia_chi'      => $data['dia_chi'] ?? '',
            'thong_tin_rieng_ngay_cap_cccd' => $data['ngay_cap_cccd'] ?? '',
        ];

        if (isset($data['thua']) && is_array($data['thua']) && !empty($data['thua'])) {
            foreach ($data['thua'] as $i => $thua) {
                $index = $i + 1;
                $values["thong_tin_rieng_thua_{$index}_to"]        = $thua['to'] ?? '';
                $values["thong_tin_rieng_thua_{$index}_thua"]      = $thua['thua'] ?? '';
                $values["thong_tin_rieng_thua_{$index}_dien_tich"] = $thua['dien_tich'] ?? '';
                $values["thong_tin_rieng_thua_{$index}_ghi_chu"]   = $thua['ghi_chu'] ?? '';
            }
        }

        // dd($values);

        $template->setValues($values);

        $fileName = 'ho_so_' . ($hs->ma_ho_so ?: 'HS_' . $hs->id) . '_' . time() . '.docx';
        $tempDir  = storage_path('app/temp');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir . '/' . $fileName;
        $template->saveAs($tempPath);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'ho_so_id'    => 'required|exists:ho_sos,id',
            'mau_word_id' => 'required|exists:mau_words,id',
        ]);

        $hs = HoSo::with([
            'xa',
            'nguoiThamTra',
            'loaiHoSo',
            'loaiThuTuc',
        ])->findOrFail($request->ho_so_id);

        $mau = MauWord::findOrFail($request->mau_word_id);
        
        $templatePath = Storage::disk('public')->path($mau->file_path);
        if (!file_exists($templatePath)) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy file mẫu Word'], 404);
        }

        $template = new TemplateProcessor($templatePath);

        $values = [
            'id'               => $hs->id,
            'ma_ho_so'         => $hs->ma_ho_so ?? '',
            'xung_ho'          => $hs->xung_ho ?? '',
            'ten_chu_ho_so'    => $hs->ten_chu_ho_so ?? '',
            'sdt_chu_ho_so'    => $hs->sdt_chu_ho_so ?? '',
            'ngay_cap_gcn'     => optional($hs->ngay_cap_gcn)->format('d/m/Y') ?? '',
            'so_vao_so'        => $hs->so_vao_so ?? '',
            'so_phat_hanh'     => $hs->so_phat_hanh ?? '',
            'xa_ap_thon'       => $hs->xa_ap_thon ?? '',
            'ghi_chu'          => $hs->ghi_chu ?? '',
            'trang_thai'       => $hs->trang_thai ?? '',
            'han_giai_quyet'   => optional($hs->han_giai_quyet)->format('d/m/Y') ?? '',
            'created_at'       => optional($hs->created_at)->format('d/m/Y') ?? '',
            'updated_at'       => optional($hs->updated_at)->format('d/m/Y H:i') ?? '',
        ];

        $values += [
            'xa'               => optional($hs->xa)->name ?? '',
            'nguoi_tham_tra'   => optional($hs->nguoiThamTra)->name ?? '',
            'loai_ho_so'       => optional($hs->loaiHoSo)->name ?? '',
            'loai_thu_tuc'     => optional($hs->loaiThuTuc)->name ?? '',
        ];

        $chuSuDung = $hs->chu_su_dung ?? [];
        $values += [
            'chu_su_dung_ho_ten'   => $chuSuDung['ho_ten'] ?? '',
            'chu_su_dung_cccd'     => $chuSuDung['cccd'] ?? '',
            'chu_su_dung_ngay_cap' => $chuSuDung['ngay_cap'] ?? '',
            'chu_su_dung_dia_chi'  => $chuSuDung['dia_chi'] ?? '',
        ];

        $uyQuyen = $hs->uy_quyen ?? [];
        $values += [
            'uy_quyen_nguoi'    => $uyQuyen['nguoi'] ?? '',
            'uy_quyen_giay_to'  => $uyQuyen['giay'] ?? '',
        ];

        if (is_array($hs->thua_chung) && !empty($hs->thua_chung)) {
            foreach ($hs->thua_chung as $i => $thua) {
                $index = $i + 1;
                $values["thua_chung_{$index}_to"]        = $thua['to'] ?? '';
                $values["thua_chung_{$index}_thua"]      = $thua['thua'] ?? '';
                $values["thua_chung_{$index}_dien_tich"] = $thua['dien_tich'] ?? '';
            }
        }

        $thongTinRieng = $hs->thong_tin_rieng ?? [];
        $values['thong_tin_rieng_loai'] = $thongTinRieng['loai'] ?? '';

        $data = $thongTinRieng['data'] ?? [];
        $values += [
            'thong_tin_rieng_ho_ten'       => $data['ho_ten'] ?? '',
            'thong_tin_rieng_cccd'         => $data['cccd'] ?? '',
            'thong_tin_rieng_dia_chi'      => $data['dia_chi'] ?? '',
            'thong_tin_rieng_ngay_cap_cccd' => $data['ngay_cap_cccd'] ?? '',
        ];

        if (isset($data['thua']) && is_array($data['thua']) && !empty($data['thua'])) {
            foreach ($data['thua'] as $i => $thua) {
                $index = $i + 1;
                $values["thong_tin_rieng_thua_{$index}_to"]        = $thua['to'] ?? '';
                $values["thong_tin_rieng_thua_{$index}_thua"]      = $thua['thua'] ?? '';
                $values["thong_tin_rieng_thua_{$index}_dien_tich"] = $thua['dien_tich'] ?? '';
                $values["thong_tin_rieng_thua_{$index}_ghi_chu"]   = $thua['ghi_chu'] ?? '';
            }
        }

        // dd($values);

        $template->setValues($values);

        $fileName = 'preview_' . time() . '_' . ($hs->ma_ho_so ?: 'HS_' . $hs->id) . '.docx';
        $tempDir  = storage_path('app/public/temp');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir . '/' . $fileName;
        $template->saveAs($tempPath);

        $publicUrl = asset('storage/temp/' . $fileName);

        return response()->json([
            'success' => true,
            'url'     => $publicUrl,
        ]);
    }
}
