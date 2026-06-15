<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoTheoDoiGroup extends Model
{
    use HasFactory;

    protected $table = "so_theo_doi_groups";

    protected $fillable = [
        'book_name',
        'book_code',
        'description',
        'creator_id'
    ];

    public function hoSos()
    {
        return $this->belongsToMany(HoSo::class, 'ho_so_so_theo_doi', 'tracking_book_id', 'ho_so_id')
            ->withPivot(['notes', 'order_index'])
            ->withTimestamps()
            ->orderBy('ho_so_so_theo_doi.order_index');
    }

    public function nguoiTao()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->book_code) {
                $max = static::max('id') + 1;
                $model->book_code = 'SO-' . str_pad($max, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
