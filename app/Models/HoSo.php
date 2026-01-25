<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class HoSo extends Model
{
    protected $fillable = [
        'ma_ho_so',
        'xung_ho',
        'ten_chu_ho_so',
        'sdt_chu_ho_so',
        'loai_ho_so_id',
        'loai_thu_tuc_id',
        'xa_id',
        'nguoi_tham_tra_id',
        'chu_su_dung',
        'uy_quyen',
        'thua_chung',
        'ngay_cap_gcn',
        'so_vao_so',
        'so_phat_hanh',
        'xa_ap_thon',
        'thong_tin_rieng',
        'ghi_chu',
        'han_giai_quyet',
        'trang_thai',
    ];

    protected $casts = [
        'chu_su_dung'       => 'array',
        'uy_quyen'          => 'array',
        'thua_chung'        => 'array',
        'thong_tin_rieng'   => 'array',
        'han_giai_quyet'    => 'date:Y-m-d',
        'ngay_cap_gcn'      => 'date:Y-m-d',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(HoSoFile::class);
    }

    public function chuSuDung()
    {
        return $this->belongsTo(User::class, 'chu_su_dung_id');
    }

    public function loaiHoSo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\LoaiHoSo::class, 'loai_ho_so_id');
    }

    public function loaiThuTuc(): BelongsTo
    {
        return $this->belongsTo(\App\Models\LoaiThuTuc::class, 'loai_thu_tuc_id');
    }

    public function xa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Xa::class, 'xa_id');
    }

    public function nguoiThamTra(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'nguoi_tham_tra_id');
    }

    public function soTheoDoiGroups()
    {
        return $this->belongsToMany(SoTheoDoiGroup::class, 'ho_so_so_theo_doi');
    }

    public function getTrangThaiMetaAttribute()
    {
        if ($this->trang_thai === 'hoan_thanh') {
            return [
                'text'  => 'Hoàn thành',
                'color' => 'success',
            ];
        }

        $map = [
            'dang_giai_quyet'       => 'Đang giải quyết',
            'cho_bo_sung'           => 'Chờ bổ sung',
            'khong_du_dieu_kien'    => 'Không đủ điều kiện',
            'chuyen_thue'           => 'Chuyển thuế',
            'hs_niem_yet_xa'        => 'Niêm yết xã',
            'phoi_hop_do_dac'       => 'Phối hợp đo đạc',
            'co_phieu_bao'          => 'Có phiếu báo',
            'in_gcn_qsdd'           => 'In GCN QSDĐ',
        ];

        $daysLeft = now()->diffInDays($this->han_giai_quyet, false);

        $color = match (true) {
            $daysLeft >= 5   => 'primary',
            $daysLeft >= 3   => 'info',
            $daysLeft === 2  => 'warning',
            $daysLeft === 1  => 'orange',
            $daysLeft <= 0   => 'danger',
            default          => 'secondary',
        };

        return [
            'text'  => $map[$this->trang_thai] ?? '—',
            'color' => $color,
        ];
    }

    protected static function booted()
    {
        static::deleting(function ($hoSo) {
            foreach ($hoSo->files as $file) {
                Storage::disk('public')->delete($file->duong_dan);
            }
            $hoSo->files()->delete();
        });
    }
}
