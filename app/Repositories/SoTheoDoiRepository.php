<?php

namespace App\Repositories;
use App\Models\SoTheoDoiGroup;
use App\Models\HoSo;

class SoTheoDoiRepository
{
    public function getPaginatedGroups() {
        return SoTheoDoiGroup::withCount('hoSos')->with('nguoiTao')->latest()->paginate(15);
    }
    public function createGroup(array $data) {
        return SoTheoDoiGroup::create($data);
    }
    public function updateGroup(SoTheoDoiGroup $group, array $data) {
        return $group->update($data);
    }
    public function deleteGroup(SoTheoDoiGroup $group) {
        return $group->delete();
    }
    public function getHoSosTrongSo(SoTheoDoiGroup $group) {
        return $group->hoSos()->with(['loaiHoSo', 'loaiThuTuc', 'xa'])->paginate(20);
    }
    public function getHoSosChuaThem(SoTheoDoiGroup $group) {
        $ids = $group->hoSos()->pluck('ho_sos.id');
        return HoSo::whereNotIn('id', $ids)->select('id', 'dossier_code', 'owner_name')->orderBy('dossier_code')->get();
    }
    public function searchHoSoChuaThem(SoTheoDoiGroup $group, $keyword) {
        $ids = $group->hoSos()->pluck('ho_sos.id');
        return HoSo::whereNotIn('id', $ids)
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($query) use ($keyword) {
                    $query->where('dossier_code', 'like', "%{$keyword}%")
                          ->orWhere('owner_name', 'like', "%{$keyword}%");
                });
            })->orderBy('dossier_code')->limit(50)->get(['id', 'dossier_code', 'owner_name']);
    }
    public function searchHoSoTrongSo(SoTheoDoiGroup $group, $keyword) {
        return $group->hoSos()
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($query) use ($keyword) {
                    $query->where('dossier_code', 'like', "%{$keyword}%")
                          ->orWhere('owner_name', 'like', "%{$keyword}%");
                });
            })->with(['loaiHoSo', 'loaiThuTuc', 'nguoiThamTra'])->limit(50)->get();
    }
}
