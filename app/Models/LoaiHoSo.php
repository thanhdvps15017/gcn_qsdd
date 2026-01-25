<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoaiHoSo extends Model
{
    use HasFactory;
    protected $table = 'loai_ho_sos';
    protected $fillable = ['name'];
}
