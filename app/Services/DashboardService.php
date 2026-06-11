<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use App\Models\HoSo;

class DashboardService
{
    protected $repo;

    public function __construct(DashboardRepository $repo) {
        $this->repo = $repo;
    }

    public function getDashboardData() {
        return [
            'tongHoSo' => $this->repo->getTongHoSo(),
            'hoanThanh' => $this->repo->getHoanThanh(),
            'dangXuLy' => $this->repo->getDangXuLy(),
            'quaHan' => $this->repo->getQuaHan(),
            'sapHetHan' => $this->repo->getSapHetHan(),
            'hoSoGap' => $this->repo->getHoSoGap(),
            'topNguoiThamTra' => $this->repo->getTopNguoiThamTra(),
            'theoXa' => $this->repo->getTheoXa(),
            'theoTrangThai' => $this->repo->getTheoTrangThai()->map(function ($item) {
                $meta = (new HoSo(['status' => $item->status]))->trang_thai_meta;
                $item->text = $meta['text'];
                $item->color = $meta['color'];
                return $item;
            }),
            'theoThang' => $this->repo->getTheoThang(),
        ];
    }
}
