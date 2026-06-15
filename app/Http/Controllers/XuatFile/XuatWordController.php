<?php

namespace App\Http\Controllers\XuatFile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\XuatWordService;
use App\Models\HoSo;
use App\Models\MauWord;
use App\Models\MauWordFolder;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

class XuatWordController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $hoSos = HoSo::with('xa')
            ->when($search, function ($query) use ($search) {
                $query->where('dossier_code', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%")
                    ->orWhere('owner_phone', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        $folders = MauWordFolder::with('mauWords')
            ->orderBy('name')
            ->get();

        return view('xuat-file.word.index', compact('hoSos', 'folders'));
    }

    public function export(Request $request) {
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
            'ma_ho_so'         => $hs->dossier_code ?? '',
            'xung_ho'          => $hs->salutation ?? '',
            'ten_chu_ho_so'    => $hs->owner_name ?? '',
            'sdt_chu_ho_so'    => $hs->owner_phone ?? '',
            'ngay_cap_gcn'     => $hs->certificate_issue_date ? $hs->certificate_issue_date->format('d/m/Y') : '',
            'so_vao_so'        => $hs->registration_book_number ?? '',
            'so_phat_hanh'     => $hs->publication_number ?? '',
            'xa_ap_thon'       => $hs->address_details ?? '',
            'ghi_chu'          => $hs->notes ?? '',
            'trang_thai'       => $hs->trang_thai_meta['text'] ?? ($hs->status ?? ''),
            'han_giai_quyet'   => $hs->deadline ? $hs->deadline->format('d/m/Y') : '',
            'created_at'       => optional($hs->created_at)->format('d/m/Y') ?? '',
            'updated_at'       => optional($hs->updated_at)->format('d/m/Y H:i') ?? '',
        ];

        $values += [
            'xa'               => optional($hs->xa)->name ?? '',
            'nguoi_tham_tra'   => optional($hs->nguoiThamTra)->name ?? '',
            'loai_ho_so'       => optional($hs->loaiHoSo)->name ?? '',
            'loai_thu_tuc'     => optional($hs->loaiThuTuc)->name ?? '',
        ];

        $landOwners = $hs->land_owners ?? [];
        $chuSuDungFirst = $landOwners[0] ?? [];
        $values += [
            'chu_su_dung_ho_ten'   => $chuSuDungFirst['full_name'] ?? '',
            'chu_su_dung_cccd'     => $chuSuDungFirst['id_card'] ?? '',
            'chu_su_dung_ngay_cap' => $chuSuDungFirst['issue_date'] ?? '',
            'chu_su_dung_dia_chi'  => $chuSuDungFirst['address'] ?? '',
        ];

        $uyQuyen = $hs->authorization ?? [];
        $values += [
            'uy_quyen_nguoi'    => $uyQuyen['person'] ?? '',
            'uy_quyen_giay_to'  => $uyQuyen['paper'] ?? '',
        ];

        $thuaChung = $hs->shared_plots ?? [];
        if (is_array($thuaChung) && !empty($thuaChung)) {
            foreach ($thuaChung as $i => $thua) {
                $index = $i + 1;
                $values["thua_chung_{$index}_to"]        = $thua['map_sheet'] ?? '';
                $values["thua_chung_{$index}_thua"]      = $thua['plot_number'] ?? '';
                $values["thua_chung_{$index}_dien_tich"] = $thua['area'] ?? '';
            }
        }

        $thongTinRieng = $hs->private_info ?? [];
        $values['thong_tin_rieng_loai'] = $thongTinRieng['type'] ?? '';

        $data = $thongTinRieng['data'] ?? [];
        $relatedList = $data['related_person'] ?? [];
        $relatedFirst = $relatedList[0] ?? [];
        $values += [
            'thong_tin_rieng_ho_ten'       => $relatedFirst['full_name'] ?? '',
            'thong_tin_rieng_cccd'         => $relatedFirst['id_card'] ?? '',
            'thong_tin_rieng_dia_chi'      => $relatedFirst['address'] ?? '',
            'thong_tin_rieng_ngay_cap_cccd' => $relatedFirst['id_issue_date'] ?? '',
        ];

        $thuaList = $data['plot_number'] ?? [];
        if (is_array($thuaList) && !empty($thuaList)) {
            foreach ($thuaList as $i => $thua) {
                $index = $i + 1;
                $values["thong_tin_rieng_thua_{$index}_to"]        = $thua['map_sheet'] ?? '';
                $values["thong_tin_rieng_thua_{$index}_thua"]      = $thua['plot_number'] ?? '';
                $values["thong_tin_rieng_thua_{$index}_dien_tich"] = $thua['area'] ?? '';
                $values["thong_tin_rieng_thua_{$index}_ghi_chu"]   = $thua['notes'] ?? '';
            }
        }

        $template->setValues($values);

        $fileName = 'ho_so_' . ($hs->dossier_code ? str_replace('/', '_', $hs->dossier_code) : 'HS_' . $hs->id) . '_' . time() . '.docx';
        $tempDir  = storage_path('app/temp');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir . '/' . $fileName;
        $template->saveAs($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    public function preview(Request $request) {
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
            'ma_ho_so'         => $hs->dossier_code ?? '',
            'xung_ho'          => $hs->salutation ?? '',
            'ten_chu_ho_so'    => $hs->owner_name ?? '',
            'sdt_chu_ho_so'    => $hs->owner_phone ?? '',
            'ngay_cap_gcn'     => $hs->certificate_issue_date ? $hs->certificate_issue_date->format('d/m/Y') : '',
            'so_vao_so'        => $hs->registration_book_number ?? '',
            'so_phat_hanh'     => $hs->publication_number ?? '',
            'xa_ap_thon'       => $hs->address_details ?? '',
            'ghi_chu'          => $hs->notes ?? '',
            'trang_thai'       => $hs->trang_thai_meta['text'] ?? ($hs->status ?? ''),
            'han_giai_quyet'   => $hs->deadline ? $hs->deadline->format('d/m/Y') : '',
            'created_at'       => optional($hs->created_at)->format('d/m/Y') ?? '',
            'updated_at'       => optional($hs->updated_at)->format('d/m/Y H:i') ?? '',
        ];

        $values += [
            'xa'               => optional($hs->xa)->name ?? '',
            'nguoi_tham_tra'   => optional($hs->nguoiThamTra)->name ?? '',
            'loai_ho_so'       => optional($hs->loaiHoSo)->name ?? '',
            'loai_thu_tuc'     => optional($hs->loaiThuTuc)->name ?? '',
        ];

        $landOwners = $hs->land_owners ?? [];
        $chuSuDungFirst = $landOwners[0] ?? [];
        $values += [
            'chu_su_dung_ho_ten'   => $chuSuDungFirst['full_name'] ?? '',
            'chu_su_dung_cccd'     => $chuSuDungFirst['id_card'] ?? '',
            'chu_su_dung_ngay_cap' => $chuSuDungFirst['issue_date'] ?? '',
            'chu_su_dung_dia_chi'  => $chuSuDungFirst['address'] ?? '',
        ];

        $uyQuyen = $hs->authorization ?? [];
        $values += [
            'uy_quyen_nguoi'    => $uyQuyen['person'] ?? '',
            'uy_quyen_giay_to'  => $uyQuyen['paper'] ?? '',
        ];

        $thuaChung = $hs->shared_plots ?? [];
        if (is_array($thuaChung) && !empty($thuaChung)) {
            foreach ($thuaChung as $i => $thua) {
                $index = $i + 1;
                $values["thua_chung_{$index}_to"]        = $thua['map_sheet'] ?? '';
                $values["thua_chung_{$index}_thua"]      = $thua['plot_number'] ?? '';
                $values["thua_chung_{$index}_dien_tich"] = $thua['area'] ?? '';
            }
        }

        $thongTinRieng = $hs->private_info ?? [];
        $values['thong_tin_rieng_loai'] = $thongTinRieng['type'] ?? '';

        $data = $thongTinRieng['data'] ?? [];
        $relatedList = $data['related_person'] ?? [];
        $relatedFirst = $relatedList[0] ?? [];
        $values += [
            'thong_tin_rieng_ho_ten'       => $relatedFirst['full_name'] ?? '',
            'thong_tin_rieng_cccd'         => $relatedFirst['id_card'] ?? '',
            'thong_tin_rieng_dia_chi'      => $relatedFirst['address'] ?? '',
            'thong_tin_rieng_ngay_cap_cccd' => $relatedFirst['id_issue_date'] ?? '',
        ];

        $thuaList = $data['plot_number'] ?? [];
        if (is_array($thuaList) && !empty($thuaList)) {
            foreach ($thuaList as $i => $thua) {
                $index = $i + 1;
                $values["thong_tin_rieng_thua_{$index}_to"]        = $thua['map_sheet'] ?? '';
                $values["thong_tin_rieng_thua_{$index}_thua"]      = $thua['plot_number'] ?? '';
                $values["thong_tin_rieng_thua_{$index}_dien_tich"] = $thua['area'] ?? '';
                $values["thong_tin_rieng_thua_{$index}_ghi_chu"]   = $thua['notes'] ?? '';
            }
        }

        $template->setValues($values);

        $fileName = 'preview_' . time() . '_' . ($hs->dossier_code ? str_replace('/', '_', $hs->dossier_code) : 'HS_' . $hs->id) . '.docx';
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
