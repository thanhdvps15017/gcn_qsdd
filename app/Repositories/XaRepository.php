<?php

namespace App\Repositories;

use App\Models\Xa;

class XaRepository
{
    public function getAll()
    {
        return Xa::orderBy('id', 'desc')->get();
    }

    public function create(array $data)
    {
        return Xa::create($data);
    }

    public function update($id, array $data)
    {
        $xa = Xa::findOrFail($id);
        $xa->update($data);
        return $xa;
    }

    public function delete($id)
    {
        $xa = Xa::findOrFail($id);
        return $xa->delete();
    }
}
