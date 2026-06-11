<?php

namespace App\Repositories;

use App\Models\HoSo;
use App\Models\MauWordFolder;
use App\Models\MauWord;

class XuatWordRepository
{
    public function getHoSos($search) {
        return HoSo::with('xa')
            ->when($search, function ($query) use ($search) {
                $query->where('dossier_code', 'like', "%{$search}%")
                      ->orWhere('owner_name', 'like', "%{$search}%")
                      ->orWhere('owner_phone', 'like', "%{$search}%");
            })->orderBy('id', 'desc')->paginate(20);
    }

    public function getFolders() { 
        return MauWordFolder::with('mauWords')->orderBy('name')->get(); 
    }

    public function getHoSoDetail($id) { 
        return HoSo::with(['xa', 'nguoiThamTra', 'loaiHoSo', 'loaiThuTuc'])->findOrFail($id); 
    }

    public function getMauWord($id) { 
        return MauWord::findOrFail($id); 
    }
}
