<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoaiThuTuc extends Model
{
    use HasFactory;
    protected $table = 'loai_thu_tucs';
    protected $fillable = [
        'name',
        'ngay_tra_ket_qua'
    ];
}
