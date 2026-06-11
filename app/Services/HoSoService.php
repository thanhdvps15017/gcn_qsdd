<?php

namespace App\Services;

use App\Repositories\HoSoRepository;
use App\Models\HoSo;
use App\Models\HoSoFile;
use App\Models\LoaiThuTuc;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class HoSoService
{
    protected $repo;

    public function __construct(HoSoRepository $repo) { 
        $this->repo = $repo; 
    }
    
    public function getPaginated($filters, $perPage = 20) { 
        return $this->repo->getPaginated($filters, $perPage); 
    }
    
    public function getById($id) { 
        return $this->repo->getById($id); 
    }
    
    public function createHoSo(array $data, $files) {
        $data = $this->formatHoSoData($data);
        $data['status'] = $data['status'] ?? 'dang_giai_quyet';
        
        $hoSo = $this->repo->create($data);
        $this->handleFiles($hoSo, $files);
        return $hoSo;
    }
    
    public function updateHoSo(HoSo $hoSo, array $data, $files) {
        $data = $this->formatHoSoData($data, $hoSo);
        if (empty($data['status'])) {
            $data['status'] = $hoSo->status ?? 'dang_giai_quyet';
        }
        
        $this->repo->update($hoSo, $data);
        $this->handleFiles($hoSo, $files);
        return $hoSo;
    }
    
    public function deleteHoSo(HoSo $hoSo) { 
        return $this->repo->delete($hoSo); 
    }
    
    public function deleteFile(HoSo $hoSo, HoSoFile $hoSoFile) {
        if ($hoSoFile->ho_so_id !== $hoSo->id) {
            throw new \Exception('Unauthorized');
        }
        Storage::disk('public')->delete($hoSoFile->file_path);
        $this->repo->deleteFile($hoSoFile);
    }
    
    public function updateTrangThai(HoSo $hoSo, $status) { 
        return $this->repo->update($hoSo, ['status' => $status]); 
    }

    public function saveGhiChu(HoSo $hoSo, $notes) { 
        return $this->repo->update($hoSo, ['notes' => $notes]); 
    }
    
    private function formatHoSoData(array $data, $hoSo = null) {
        // Chuẩn hóa land_owners
        $chuSuDung = $data['land_owners'] ?? [];
        if (is_array($chuSuDung) && !empty($chuSuDung)) {
            $normalized = $this->normalizeIndexedRows($chuSuDung);
            foreach ($normalized as &$item) {
                $item = array_merge([
                    'salutation'   => 'Ông',
                    'full_name'    => '',
                    'date_of_birth' => null,
                    'id_card'      => '',
                    'issue_date'  => null,
                    'address'   => '',
                ], $item);
            }
            $data['land_owners'] = $normalized;
        } else {
            $data['land_owners'] = [];
        }

        // Chuẩn hóa shared_plots
        $thuaChung = $data['shared_plots'] ?? [];
        if (is_array($thuaChung) && !empty($thuaChung)) {
            $data['shared_plots'] = $this->normalizeIndexedRows($thuaChung);
        }

        // Chuẩn hóa private_info
        $rieng = $data['private_info'] ?? ['type' => null, 'data' => []];
        $riengData = $rieng['data'] ?? [];
        
        $thuaRieng = $riengData['plot_number'] ?? [];
        if (is_array($thuaRieng) && !empty($thuaRieng)) {
            $normalizedRieng = $this->normalizeIndexedRows($thuaRieng);
            foreach ($normalizedRieng as &$item) {
                $item = array_merge([
                    'map_sheet'   => '', 
                    'plot_number' => '', 
                    'area'        => null, 
                    'notes'       => ''
                ], (array)$item);
            }
            $rieng['data']['plot_number'] = $normalizedRieng;
        }

        $nguoiLienQuan = $riengData['related_person'] ?? [];
        if (is_array($nguoiLienQuan) && !empty($nguoiLienQuan)) {
            $rieng['data']['related_person'] = $this->normalizeIndexedRows($nguoiLienQuan);
        }
        $data['private_info'] = $rieng;

        // Tính hạn giải quyết
        if (!empty($data['procedure_type_id']) && empty($data['deadline'])) {
            $loaiThuTuc = LoaiThuTuc::find($data['procedure_type_id']);
            if ($loaiThuTuc && $loaiThuTuc->processing_days !== null) {
                $han = Carbon::today()->addWeekdays((int) $loaiThuTuc->processing_days);
                $data['deadline'] = $han->toDateString();
            }
        }
        
        return $data;
    }
    
    private function handleFiles(HoSo $hoSo, $files) {
        if ($files) {
            foreach ($files as $file) {
                $path = $file->store('ho_so_files/' . $hoSo->id, 'public');
                $hoSo->files()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }
    }
    
    private function normalizeIndexedRows(array $raw): array {
        if (!empty($raw) && is_array(reset($raw)) && count(reset($raw)) > 1) {
            return array_values($raw);
        }
        
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
        
        return array_values(array_filter($rows, function ($row) { 
            return is_array($row) && !empty(array_filter($row)); 
        }));
    }
}
