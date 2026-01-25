<?php

namespace App\Http\Controllers\XuatFile;

use App\Http\Controllers\Controller;
use App\Models\HoSo;
use App\Models\MauWord;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;

class XuatWordController extends Controller
{
    /**
     * Danh sách hồ sơ + danh sách mẫu word
     */
    public function index()
    {
        $hoSos    = HoSo::with(['xa'])->latest()->paginate(20);
        $mauWords = MauWord::latest()->get();

        return view('xuat-file.word.index', compact('hoSos', 'mauWords'));
    }

    /**
     * Xuất Word theo hồ sơ + mẫu
     */
    public function export(Request $request)
    {
        $request->validate([
            'ho_so_id'    => 'required|exists:ho_sos,id',
            'mau_word_id' => 'required|exists:mau_words,id',
        ]);

        // ===== LẤY DỮ LIỆU =====
        $hs = HoSo::with([
            'xa',
            'nguoiThamTra',
            'loaiHoSo',
            'loaiThuTuc',
        ])->findOrFail($request->ho_so_id);

        $mau = MauWord::findOrFail($request->mau_word_id);

        $templatePath = storage_path('app/' . $mau->file_path);
        if (!file_exists($templatePath)) {
            abort(404, 'Không tìm thấy file mẫu Word');
        }

        $template = new TemplateProcessor($templatePath);

        /**
         * =====================================================
         * MAP KEY WORD
         * =====================================================
         */

        // ===== FIELD CƠ BẢN =====
        $values = [
            'id'               => $hs->id,
            'ma_ho_so'         => $hs->ma_ho_so,
            'xung_ho'          => $hs->xung_ho,
            'ten_chu_ho_so'    => $hs->ten_chu_ho_so,
            'sdt_chu_ho_so'    => $hs->sdt_chu_ho_so,
            'ngay_cap_gcn'     => optional($hs->ngay_cap_gcn)->format('d/m/Y'),
            'so_vao_so'        => $hs->so_vao_so,
            'so_phat_hanh'     => $hs->so_phat_hanh,
            'xa_ap_thon'       => $hs->xa_ap_thon,
            'ghi_chu'          => $hs->ghi_chu,
            'trang_thai'       => $hs->trang_thai,
            'han_giai_quyet'   => optional($hs->han_giai_quyet)->format('d/m/Y'),
            'created_at'       => optional($hs->created_at)->format('d/m/Y'),
        ];

        // ===== QUAN HỆ =====
        $values += [
            'xa'               => optional($hs->xa)->ten,
            'nguoi_tham_tra'   => optional($hs->nguoiThamTra)->name,
            'loai_ho_so'       => optional($hs->loaiHoSo)->ten,
            'loai_thu_tuc'     => optional($hs->loaiThuTuc)->ten,
        ];

        // ===== JSON: CHỦ SỬ DỤNG =====
        $chuSuDung = $hs->chu_su_dung ?? [];
        $values += [
            'chu_su_dung_ho_ten'   => $chuSuDung['ho_ten'] ?? '',
            'chu_su_dung_cccd'     => $chuSuDung['cccd'] ?? '',
            'chu_su_dung_ngay_cap' => $chuSuDung['ngay_cap'] ?? '',
            'chu_su_dung_dia_chi'  => $chuSuDung['dia_chi'] ?? '',
        ];

        // ===== JSON: ỦY QUYỀN =====
        $uyQuyen = $hs->uy_quyen ?? [];
        $values += [
            'uy_quyen_nguoi' => $uyQuyen['nguoi'] ?? '',
            'uy_quyen_giay'  => $uyQuyen['giay'] ?? '',
        ];

        // ===== ARRAY JSON: THỬA CHUNG =====
        if (is_array($hs->thua_chung)) {
            foreach ($hs->thua_chung as $i => $thua) {
                $index = $i + 1;
                $values["thua_chung_{$index}_to"]        = $thua['to'] ?? '';
                $values["thua_chung_{$index}_thua"]      = $thua['thua'] ?? '';
                $values["thua_chung_{$index}_dien_tich"] = $thua['dien_tich'] ?? '';
            }
        }

        // ===== JSON PHỨC TẠP: THÔNG TIN RIÊNG =====
        $thongTinRieng = $hs->thong_tin_rieng ?? [];
        $values['thong_tin_rieng_loai'] = $thongTinRieng['loai'] ?? '';

        $data = $thongTinRieng['data'] ?? [];
        $values += [
            'thong_tin_rieng_ho_ten' => $data['ho_ten'] ?? '',
            'thong_tin_rieng_cccd'   => $data['cccd'] ?? '',
            'thong_tin_rieng_dia_chi' => $data['dia_chi'] ?? '',
        ];

        // ===== GÁN VÀO TEMPLATE =====
        // dd($values);

        $template->setValues($values);

        /**
         * =====================================================
         * LƯU FILE & DOWNLOAD
         * =====================================================
         */

        $fileName = 'ho_so_' . $hs->ma_ho_so . '.docx';
        $tempDir  = storage_path('app/temp');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir . '/' . $fileName;

        $template->saveAs($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}
