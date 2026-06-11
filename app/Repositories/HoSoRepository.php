<?php

namespace App\Repositories;

use App\Models\HoSo;
use App\Models\HoSoFile;

class HoSoRepository
{
    public function getPaginated($filters, $perPage = 20) {
        $query = HoSo::query();
        
        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('dossier_code', 'like', "%{$q}%")
                    ->orWhere('owner_name', 'like', "%{$q}%");
            });
        }
        
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['dossier_type_id'])) $query->where('dossier_type_id', $filters['dossier_type_id']);
        if (!empty($filters['procedure_type_id'])) $query->where('procedure_type_id', $filters['procedure_type_id']);
        if (!empty($filters['ward_id'])) $query->where('ward_id', $filters['ward_id']);
        if (!empty($filters['inspector_id'])) $query->where('inspector_id', $filters['inspector_id']);
        
        $sort = $filters['sort'] ?? 'desc';
        
        return $query->orderBy('created_at', $sort)
            ->with(['loaiHoSo', 'loaiThuTuc', 'xa', 'nguoiThamTra'])
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data) { 
        return HoSo::create($data); 
    }

    public function getById($id) { 
        return HoSo::with(['loaiHoSo', 'loaiThuTuc', 'xa', 'nguoiThamTra', 'files', 'trangThaiLogs.user'])->findOrFail($id); 
    }

    public function update(HoSo $hoSo, array $data) { 
        return $hoSo->update($data); 
    }

    public function delete(HoSo $hoSo) { 
        return $hoSo->delete(); 
    }

    public function getFileById($id) { 
        return HoSoFile::findOrFail($id); 
    }

    public function deleteFile(HoSoFile $file) { 
        return $file->delete(); 
    }
}
