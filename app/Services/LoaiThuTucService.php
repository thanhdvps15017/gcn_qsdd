<?php

namespace App\Services;
use App\Repositories\LoaiThuTucRepository;

class LoaiThuTucService
{
    protected $repo;
    public function __construct(LoaiThuTucRepository $repo) {
        $this->repo = $repo;
    }
    public function getAll() { return $this->repo->getAll(); }
    public function create(array $data) { return $this->repo->create($data); }
    public function update($id, array $data) { return $this->repo->update($id, $data); }
    public function delete($id) { return $this->repo->delete($id); }
}
