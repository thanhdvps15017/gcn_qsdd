<?php

namespace App\Services;
use App\Repositories\SoTheoDoiRepository;
use App\Models\SoTheoDoiGroup;
use App\Models\HoSo;

class SoTheoDoiService
{
    protected $repo;
    public function __construct(SoTheoDoiRepository $repo) {
        $this->repo = $repo;
    }
    public function getPaginatedGroups() { return $this->repo->getPaginatedGroups(); }
    public function createGroup(array $data) { return $this->repo->createGroup($data); }
    public function updateGroup(SoTheoDoiGroup $group, array $data) { return $this->repo->updateGroup($group, $data); }
    public function deleteGroup(SoTheoDoiGroup $group) { return $this->repo->deleteGroup($group); }
    public function getHoSosTrongSo(SoTheoDoiGroup $group) { return $this->repo->getHoSosTrongSo($group); }
    public function getHoSosChuaThem(SoTheoDoiGroup $group) { return $this->repo->getHoSosChuaThem($group); }
    
    public function batchAddHoSo(SoTheoDoiGroup $group, array $hoSoIds) {
        $existingIds = $group->hoSos()->pluck('ho_sos.id')->toArray();
        $newHoSoIds = array_values(array_diff($hoSoIds, $existingIds));
        if (empty($newHoSoIds)) return 0;

        $todayPrefix = now()->format('dmy');
        $lastThuTu = $group->hoSos()
            ->where('ho_so_so_theo_doi.order_index', 'like', $todayPrefix . '-%')
            ->orderByDesc('ho_so_so_theo_doi.order_index')
            ->value('ho_so_so_theo_doi.order_index');

        $startNumber = $lastThuTu ? (int) substr($lastThuTu, -6) : 0;

        foreach ($newHoSoIds as $index => $hoSoId) {
            $number = $startNumber + $index + 1;
            $thuTu = $todayPrefix . '-' .$group->id . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
            $group->hoSos()->attach($hoSoId, ['order_index' => $thuTu]);
        }
        return count($newHoSoIds);
    }

    public function batchRemoveHoSo(SoTheoDoiGroup $group, array $hoSoIds) {
        $group->hoSos()->detach($hoSoIds);
        return count($hoSoIds);
    }

    public function searchHoSoChuaThem(SoTheoDoiGroup $group, $keyword) {
        return $this->repo->searchHoSoChuaThem($group, $keyword);
    }

    public function searchHoSoTrongSo(SoTheoDoiGroup $group, $keyword) {
        return $this->repo->searchHoSoTrongSo($group, $keyword);
    }

    public function saveGhiChu(SoTheoDoiGroup $group, HoSo $hoSo, $notes) {
        if (! $group->hoSos()->where('ho_so_id', $hoSo->id)->exists()) {
            return false;
        }
        $group->hoSos()->updateExistingPivot($hoSo->id, ['notes' => $notes]);
        return true;
    }
}
