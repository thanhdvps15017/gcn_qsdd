<?php

namespace App\Repositories;
use App\Models\LoaiThuTuc;

class LoaiThuTucRepository
{
    public function getAll() {
        return LoaiThuTuc::orderBy('id', 'desc')->get();
    }
    public function create(array $data) {
        return LoaiThuTuc::create($data);
    }
    public function update($id, array $data) {
        $item = LoaiThuTuc::findOrFail($id);
        $item->update($data);
        return $item;
    }
    public function delete($id) {
        return LoaiThuTuc::findOrFail($id)->delete();
    }
}
