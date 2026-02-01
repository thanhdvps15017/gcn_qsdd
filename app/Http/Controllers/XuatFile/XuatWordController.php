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
    /**
     * Chuyển chuỗi sang Title Case, hỗ trợ tiếng Việt (UTF-8)
     */
    private function titleCaseVietnamese(?string $str): string
    {
        if (empty($str)) {
            return '';
        }

        // Chuyển hết về lowercase trước
        $str = mb_strtolower(trim($str), 'UTF-8');

        // ucwords cho từng từ (viết hoa chữ cái đầu mỗi từ)
        $str = mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');

        // Có thể thêm quy tắc đặc biệt nếu cần (ví dụ: không viết hoa "và", "của", "phường",...)
        // Hiện tại giữ đơn giản, phù hợp hầu hết trường hợp tên người + địa chỉ
        return $str;
    }

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
            // Thông tin cơ bản
            'Id'               => $hs->id ?? '',
            'Ma_Ho_So'         => $hs->ma_ho_so ?? '',
            'Ten_Chu_Ho_So'    => $this->titleCaseVietnamese($hs->ten_chu_ho_so ?? ''),
            'Sdt_Chu_Ho_So'    => $hs->sdt_chu_ho_so ?? '',
            'Ngay_Cap_Gcn'     => optional($hs->ngay_cap_gcn)->format('d/m/Y') ?? '',
            'So_Vao_So'        => $hs->so_vao_so ?? '',
            'So_Phat_Hanh'     => $hs->so_phat_hanh ?? '',
            'Ghi_Chu'          => $this->titleCaseVietnamese($hs->ghi_chu ?? ''),
            'Trang_Thai'       => $this->titleCaseVietnamese($hs->trang_thai ?? ''),
            'Han_Giai_Quyet'   => optional($hs->han_giai_quyet)->format('d/m/Y') ?? '',
            'Created_At'       => optional($hs->created_at)->format('d/m/Y') ?? '',
            'Updated_At'       => optional($hs->updated_at)->format('d/m/Y H:i') ?? '',

            'Xa'               => $this->titleCaseVietnamese(optional($hs->xa)->name ?? ''),
            'Nguoi_Tham_Tra'   => $this->titleCaseVietnamese(optional($hs->nguoiThamTra)->name ?? ''),
            'Loai_Ho_So'       => $this->titleCaseVietnamese(optional($hs->loaiHoSo)->name ?? ''),
            'Loai_Thu_Tuc'     => $this->titleCaseVietnamese(optional($hs->loaiThuTuc)->name ?? ''),
        ];

        // 1. Chủ sử dụng đất (nhiều người)
        if (is_array($hs->chu_su_dung) && !empty($hs->chu_su_dung)) {
            foreach ($hs->chu_su_dung as $i => $nguoi) {
                $index = $i + 1;
                $prefix = "Chu_Su_Dung_{$index}_";

                $values[$prefix . 'Ho_Ten']   = $this->titleCaseVietnamese($nguoi['ho_ten'] ?? '');
                $values[$prefix . 'Cccd']     = $nguoi['cccd'] ?? '';
                $values[$prefix . 'Dia_Chi']  = $this->titleCaseVietnamese($nguoi['dia_chi'] ?? '');
                $values[$prefix . 'Xung_Ho']  = $this->titleCaseVietnamese($nguoi['xung_ho'] ?? '');
                $values[$prefix . 'Ngay_Cap'] = $nguoi['ngay_cap'] ?? '';
                $values[$prefix . 'Ngay_Sinh'] = $nguoi['ngay_sinh'] ?? '';
            }
        }

        // 2. Ủy quyền
        $uyQuyen = $hs->uy_quyen ?? [];
        $values += [
            'Uy_Quyen_Nguoi'    => $this->titleCaseVietnamese($uyQuyen['nguoi'] ?? ''),
            'Uy_Quyen_Giay_To'  => $this->titleCaseVietnamese($uyQuyen['giay'] ?? ''),
        ];

        // 3. Thửa chung
        if (is_array($hs->thua_chung) && !empty($hs->thua_chung)) {
            foreach ($hs->thua_chung as $i => $thua) {
                $index = $i + 1;
                $prefix = "Thua_Chung_{$index}_";

                $values[$prefix . 'To']        = $thua['to'] ?? '';
                $values[$prefix . 'Thua']      = $thua['thua'] ?? '';
                $values[$prefix . 'Dien_Tich'] = $thua['dien_tich'] ?? '';
                $values[$prefix . 'Ap_Thon']   = $this->titleCaseVietnamese($thua['ap_thon'] ?? '');
                $values[$prefix . 'Xa_Id']     = $thua['xa_id'] ?? '';
            }
        }

        // 4. Thông tin riêng
        $thongTinRieng = $hs->thong_tin_rieng ?? [];
        $values['Thong_Tin_Rieng_Loai'] = $this->titleCaseVietnamese($thongTinRieng['loai'] ?? '');
        // dd($values);
        $data = $thongTinRieng['data'] ?? [];

        // Thửa riêng
        if (isset($data['thua']) && is_array($data['thua']) && !empty($data['thua'])) {
            foreach ($data['thua'] as $i => $thua) {
                $index = $i + 1;
                $prefix = "Thong_Tin_Rieng_Thua_{$index}_";

                $values[$prefix . 'To']        = $thua['to'] ?? '';
                $values[$prefix . 'Thua']      = $thua['thua'] ?? '';
                $values[$prefix . 'Dien_Tich'] = $thua['dien_tich'] ?? '';
                $values[$prefix . 'Ghi_Chu']   = $this->titleCaseVietnamese($thua['ghi_chu'] ?? '');
            }
        }

        // 5. Người liên quan
        if (isset($data['nguoi_lien_quan']) && is_array($data['nguoi_lien_quan']) && !empty($data['nguoi_lien_quan'])) {
            foreach ($data['nguoi_lien_quan'] as $i => $nguoi) {
                $index = $i + 1;
                $prefix = "Thong_Tin_Rieng_Nguoi_Lien_Quan_{$index}_";

                $values[$prefix . 'Ho_Ten']   = $this->titleCaseVietnamese($nguoi['ho_ten']   ?? '');
                $values[$prefix . 'Cccd']     = $nguoi['cccd']     ?? '';
                $values[$prefix . 'Xung_Ho']  = $this->titleCaseVietnamese($nguoi['xung_ho']  ?? '');
                $values[$prefix . 'Ngay_Cap'] = $nguoi['ngay_cap'] ?? '';
                $values[$prefix . 'Ngay_Sinh'] = $nguoi['ngay_sinh'] ?? '';
                $values[$prefix . 'Dia_Chi']  = $this->titleCaseVietnamese($nguoi['dia_chi']  ?? '');
            }
        }

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

        // === Phần values giống hệt export ===
        $values = [
            'Id'               => $hs->id ?? '',
            'Ma_Ho_So'         => $hs->ma_ho_so ?? '',
            'Ten_Chu_Ho_So'    => $this->titleCaseVietnamese($hs->ten_chu_ho_so ?? ''),
            'Sdt_Chu_Ho_So'    => $hs->sdt_chu_ho_so ?? '',
            'Ngay_Cap_Gcn'     => optional($hs->ngay_cap_gcn)->format('d/m/Y') ?? '',
            'So_Vao_So'        => $hs->so_vao_so ?? '',
            'So_Phat_Hanh'     => $hs->so_phat_hanh ?? '',
            'Ghi_Chu'          => $this->titleCaseVietnamese($hs->ghi_chu ?? ''),
            'Trang_Thai'       => $this->titleCaseVietnamese($hs->trang_thai ?? ''),
            'Han_Giai_Quyet'   => optional($hs->han_giai_quyet)->format('d/m/Y') ?? '',
            'Created_At'       => optional($hs->created_at)->format('d/m/Y') ?? '',
            'Updated_At'       => optional($hs->updated_at)->format('d/m/Y H:i') ?? '',

            'Xa'               => $this->titleCaseVietnamese(optional($hs->xa)->name ?? ''),
            'Nguoi_Tham_Tra'   => $this->titleCaseVietnamese(optional($hs->nguoiThamTra)->name ?? ''),
            'Loai_Ho_So'       => $this->titleCaseVietnamese(optional($hs->loaiHoSo)->name ?? ''),
            'Loai_Thu_Tuc'     => $this->titleCaseVietnamese(optional($hs->loaiThuTuc)->name ?? ''),
        ];

        if (is_array($hs->chu_su_dung) && !empty($hs->chu_su_dung)) {
            foreach ($hs->chu_su_dung as $i => $nguoi) {
                $index = $i + 1;
                $prefix = "Chu_Su_Dung_{$index}_";

                $values[$prefix . 'Ho_Ten']   = $this->titleCaseVietnamese($nguoi['ho_ten'] ?? '');
                $values[$prefix . 'Cccd']     = $nguoi['cccd'] ?? '';
                $values[$prefix . 'Dia_Chi']  = $this->titleCaseVietnamese($nguoi['dia_chi'] ?? '');
                $values[$prefix . 'Xung_Ho']  = $this->titleCaseVietnamese($nguoi['xung_ho'] ?? '');
                $values[$prefix . 'Ngay_Cap'] = $nguoi['ngay_cap'] ?? '';
                $values[$prefix . 'Ngay_Sinh'] = $nguoi['ngay_sinh'] ?? '';
            }
        }

        $uyQuyen = $hs->uy_quyen ?? [];
        $values += [
            'Uy_Quyen_Nguoi'    => $this->titleCaseVietnamese($uyQuyen['nguoi'] ?? ''),
            'Uy_Quyen_Giay_To'  => $this->titleCaseVietnamese($uyQuyen['giay'] ?? ''),
        ];

        if (is_array($hs->thua_chung) && !empty($hs->thua_chung)) {
            foreach ($hs->thua_chung as $i => $thua) {
                $index = $i + 1;
                $prefix = "Thua_Chung_{$index}_";

                $values[$prefix . 'To']        = $thua['to'] ?? '';
                $values[$prefix . 'Thua']      = $thua['thua'] ?? '';
                $values[$prefix . 'Dien_Tich'] = $thua['dien_tich'] ?? '';
                $values[$prefix . 'Ap_Thon']   = $this->titleCaseVietnamese($thua['ap_thon'] ?? '');
                $values[$prefix . 'Xa_Id']     = $thua['xa_id'] ?? '';
            }
        }

        $thongTinRieng = $hs->thong_tin_rieng ?? [];
        $values['Thong_Tin_Rieng_Loai'] = $this->titleCaseVietnamese($thongTinRieng['loai'] ?? '');

        $data = $thongTinRieng['data'] ?? [];

        if (isset($data['thua']) && is_array($data['thua']) && !empty($data['thua'])) {
            foreach ($data['thua'] as $i => $thua) {
                $index = $i + 1;
                $prefix = "Thong_Tin_Rieng_Thua_{$index}_";

                $values[$prefix . 'To']        = $thua['to'] ?? '';
                $values[$prefix . 'Thua']      = $thua['thua'] ?? '';
                $values[$prefix . 'Dien_Tich'] = $thua['dien_tich'] ?? '';
                $values[$prefix . 'Ghi_Chu']   = $this->titleCaseVietnamese($thua['ghi_chu'] ?? '');
            }
        }

        if (isset($data['nguoi_lien_quan']) && is_array($data['nguoi_lien_quan']) && !empty($data['nguoi_lien_quan'])) {
            foreach ($data['nguoi_lien_quan'] as $i => $nguoi) {
                $index = $i + 1;
                $prefix = "Thong_Tin_Rieng_Nguoi_Lien_Quan_{$index}_";

                $values[$prefix . 'Ho_Ten']   = $this->titleCaseVietnamese($nguoi['ho_ten']   ?? '');
                $values[$prefix . 'Cccd']     = $nguoi['cccd']     ?? '';
                $values[$prefix . 'Xung_Ho']  = $this->titleCaseVietnamese($nguoi['xung_ho']  ?? '');
                $values[$prefix . 'Ngay_Cap'] = $nguoi['ngay_cap'] ?? '';
                $values[$prefix . 'Ngay_Sinh'] = $nguoi['ngay_sinh'] ?? '';
                $values[$prefix . 'Dia_Chi']  = $this->titleCaseVietnamese($nguoi['dia_chi']  ?? '');
            }
        }

        $template->setValues($values);

        $fileName = 'preview_' . time() . '_' . ($hs->ma_ho_so ?: 'HS_' . $hs->id) . '.docx';
        $tempDir  = storage_path('app/temp');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir . '/' . $fileName;
        $template->saveAs($tempPath);

        $publicUrl = asset('storage/temp/' . $fileName);

        return response()->json([
            'success'  => true,
            'url'      => $publicUrl,
            'filename' => $fileName,
        ]);
    }
}
