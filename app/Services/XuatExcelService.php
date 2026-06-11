<?php

namespace App\Services;
use App\Repositories\XuatExcelRepository;

class XuatExcelService
{
    protected $repo;

    public function __construct(XuatExcelRepository $repo) { 
        $this->repo = $repo; 
    }

    public function getHoSosForIndex(array $filters) {
        return $this->repo->getFilteredHoSos($filters, true);
    }

    public function getHoSosForExport(array $filters) {
        return $this->repo->getFilteredHoSos($filters, false);
    }
}
