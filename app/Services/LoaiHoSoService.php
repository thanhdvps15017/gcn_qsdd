<?php

namespace App\Services;

use App\Repositories\LoaiHoSoRepository;

class LoaiHoSoService
{
    protected $loaiHoSoRepository;

    public function __construct(LoaiHoSoRepository $loaiHoSoRepository)
    {
        $this->loaiHoSoRepository = $loaiHoSoRepository;
    }

    public function getAllLoaiHoSos()
    {
        return $this->loaiHoSoRepository->getAll();
    }

    public function createLoaiHoSo(array $data)
    {
        return $this->loaiHoSoRepository->create($data);
    }

    public function updateLoaiHoSo($id, array $data)
    {
        return $this->loaiHoSoRepository->update($id, $data);
    }

    public function deleteLoaiHoSo($id)
    {
        return $this->loaiHoSoRepository->delete($id);
    }
}
