<?php

namespace App\Services;

use App\Repositories\XuatWordRepository;
use PhpOffice\PhpWord\TemplateProcessor;

class XuatWordService
{
    protected $repo;

    public function __construct(XuatWordRepository $repo) { 
        $this->repo = $repo; 
    }

    public function getHoSos($search) { 
        return $this->repo->getHoSos($search); 
    }

    public function getFolders() { 
        return $this->repo->getFolders(); 
    }
    
    public function processTemplate($hoSoId, $mauWordId, $isExport = true) {
        $hs = $this->repo->getHoSoDetail($hoSoId);
        $mau = $this->repo->getMauWord($mauWordId);
        
        $templatePath = storage_path('app/' . $mau->file_path);
        if (!file_exists($templatePath)) {
            throw new \Exception('Không tìm thấy file mẫu Word');
        }
        
        $template = new TemplateProcessor($templatePath);
        
        $values = [
            'id' => $hs->id, 
            'dossier_code' => $hs->dossier_code ?? '', 
            'salutation' => $hs->salutation ?? '',
            'owner_name' => $hs->owner_name ?? '', 
            'owner_phone' => $hs->owner_phone ?? '',
            'certificate_issue_date' => optional($hs->certificate_issue_date)->format('d/m/Y') ?? '',
            'registration_book_number' => $hs->registration_book_number ?? '',
            'publication_number' => $hs->publication_number ?? '',
            'address_details' => $hs->address_details ?? '', 
            'notes' => $hs->notes ?? '',
            'status' => $hs->status ?? '', 
            'deadline' => optional($hs->deadline)->format('d/m/Y') ?? '',
            'created_at' => optional($hs->created_at)->format('d/m/Y') ?? '',
            'updated_at' => optional($hs->updated_at)->format('d/m/Y H:i') ?? '',
            'xa' => optional($hs->xa)->name ?? '', 
            'nguoi_tham_tra' => optional($hs->nguoiThamTra)->name ?? '',
            'loai_ho_so' => optional($hs->loaiHoSo)->name ?? '', 
            'loai_thu_tuc' => optional($hs->loaiThuTuc)->name ?? '',
        ];

        $chuSuDungFirst = $hs->land_owners[0] ?? [];
        $values += [
            'chu_su_dung_ho_ten' => $chuSuDungFirst['full_name'] ?? '', 
            'chu_su_dung_cccd' => $chuSuDungFirst['id_card'] ?? '',
            'chu_su_dung_ngay_cap' => $chuSuDungFirst['issue_date'] ?? '', 
            'chu_su_dung_dia_chi' => $chuSuDungFirst['address'] ?? '',
        ];

        $uyQuyen = $hs->authorization ?? [];
        $values += [
            'uy_quyen_nguoi' => $uyQuyen['person'] ?? '', 
            'uy_quyen_giay_to' => $uyQuyen['paper'] ?? ''
        ];

        if (is_array($hs->shared_plots)) {
            foreach ($hs->shared_plots as $i => $thua) {
                $values["thua_chung_" . ($i + 1) . "_to"] = $thua['map_sheet'] ?? '';
                $values["thua_chung_" . ($i + 1) . "_thua"] = $thua['plot_number'] ?? '';
                $values["thua_chung_" . ($i + 1) . "_dien_tich"] = $thua['area'] ?? '';
            }
        }

        $thongTinRieng = $hs->private_info ?? [];
        $values['thong_tin_rieng_loai'] = $thongTinRieng['type'] ?? '';
        
        $data = $thongTinRieng['data'] ?? [];
        $values += [
            'thong_tin_rieng_ho_ten' => $data['full_name'] ?? '', 
            'thong_tin_rieng_cccd' => $data['id_card'] ?? '',
            'thong_tin_rieng_dia_chi' => $data['address'] ?? '', 
            'thong_tin_rieng_ngay_cap_cccd' => $data['id_issue_date'] ?? '',
        ];

        if (isset($data['plot_number']) && is_array($data['plot_number'])) {
            foreach ($data['plot_number'] as $i => $thua) {
                $values["thong_tin_rieng_thua_" . ($i + 1) . "_to"] = $thua['map_sheet'] ?? '';
                $values["thong_tin_rieng_thua_" . ($i + 1) . "_thua"] = $thua['plot_number'] ?? '';
                $values["thong_tin_rieng_thua_" . ($i + 1) . "_dien_tich"] = $thua['area'] ?? '';
                $values["thong_tin_rieng_thua_" . ($i + 1) . "_ghi_chu"] = $thua['notes'] ?? '';
            }
        }

        $template->setValues($values);
        
        $prefix = $isExport ? 'ho_so_' : 'preview_' . time() . '_';
        $fileName = $prefix . ($hs->dossier_code ?: 'HS_' . $hs->id) . ($isExport ? '_' . time() : '') . '.docx';
        $tempDir = storage_path($isExport ? 'app/temp' : 'app/public/temp');
        
        if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);
        
        $tempPath = $tempDir . '/' . $fileName;
        $template->saveAs($tempPath);
        
        return ['path' => $tempPath, 'fileName' => $fileName];
    }
}
