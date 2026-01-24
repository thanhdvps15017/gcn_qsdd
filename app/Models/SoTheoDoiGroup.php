<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoTheoDoiGroup extends Model
{
    use HasFactory;

    protected $table = "so_theo_doi_groups";

    protected $fillable = [
        'ten_so',
        'ma_so',
        'mo_ta',
        'nguoi_tao_id'
    ];

    public function hoSos()
    {
        return $this->belongsToMany(HoSo::class, 'ho_so_so_theo_doi');
    }

    public function nguoiTao()
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->ma_so) {
                $max = static::max('id') + 1;
                $model->ma_so = 'SO-' . str_pad($max, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
