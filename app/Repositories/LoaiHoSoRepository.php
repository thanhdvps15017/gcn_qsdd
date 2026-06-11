<?php

namespace App\Repositories;

use App\Models\LoaiHoSo;

class LoaiHoSoRepository
{
    public function getAll()
    {
        return LoaiHoSo::orderBy('id', 'desc')->get();
    }

    public function create(array $data)
    {
        return LoaiHoSo::create($data);
    }

    public function update($id, array $data)
    {
        $item = LoaiHoSo::findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        $item = LoaiHoSo::findOrFail($id);
        return $item->delete();
    }
}
