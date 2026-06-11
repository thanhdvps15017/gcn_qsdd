<?php

namespace App\Services;

use App\Repositories\XaRepository;

class XaService
{
    protected $xaRepository;

    public function __construct(XaRepository $xaRepository)
    {
        $this->xaRepository = $xaRepository;
    }

    public function getAllXas()
    {
        return $this->xaRepository->getAll();
    }

    public function createXa(array $data)
    {
        return $this->xaRepository->create($data);
    }

    public function updateXa($id, array $data)
    {
        return $this->xaRepository->update($id, $data);
    }

    public function deleteXa($id)
    {
        return $this->xaRepository->delete($id);
    }
}
