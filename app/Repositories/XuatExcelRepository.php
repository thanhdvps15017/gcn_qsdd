<?php

namespace App\Repositories;
use App\Models\HoSo;

class XuatExcelRepository
{
    public function getFilteredHoSos($filters, $paginate = true) {
        $query = HoSo::query()->with(['loaiHoSo', 'loaiThuTuc', 'xa', 'nguoiThamTra']);
        
        if (!empty($filters['dossier_type_id'])) $query->where('dossier_type_id', $filters['dossier_type_id']);
        if (!empty($filters['procedure_type_id'])) $query->where('procedure_type_id', $filters['procedure_type_id']);
        if (!empty($filters['ward_id'])) $query->where('ward_id', $filters['ward_id']);
        if (!empty($filters['inspector_id'])) $query->where('inspector_id', $filters['inspector_id']);
        if (!empty($filters['created_from'])) $query->whereDate('created_at', '>=', $filters['created_from']);
        if (!empty($filters['created_to'])) $query->whereDate('created_at', '<=', $filters['created_to']);
        
        return $paginate ? $query->latest()->paginate(20)->withQueryString() : $query->get();
    }
}
