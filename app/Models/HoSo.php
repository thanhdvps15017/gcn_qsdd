<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class HoSo extends Model
{
    protected $fillable = [
        'dossier_code',
        'salutation',
        'owner_name',
        'owner_phone',
        'dossier_type_id',
        'procedure_type_id',
        'ward_id',
        'inspector_id',
        'land_owners',
        'authorization',
        'shared_plots',
        'certificate_issue_date',
        'registration_book_number',
        'publication_number',
        'address_details',
        'private_info',
        'notes',
        'deadline',
        'status',
    ];

    protected $casts = [
        'land_owners'       => 'array',
        'authorization'          => 'array',
        'shared_plots'        => 'array',
        'private_info'   => 'array',
        'deadline'    => 'date:Y-m-d',
        'certificate_issue_date'      => 'date:Y-m-d',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    public function trangThaiLogs(): HasMany
    {
        return $this->hasMany(HoSoTrangThaiLog::class)->latest();
    }

    public function files(): HasMany
    {
        return $this->hasMany(HoSoFile::class);
    }

    public function loaiHoSo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\LoaiHoSo::class, 'dossier_type_id');
    }

    public function loaiThuTuc(): BelongsTo
    {
        return $this->belongsTo(\App\Models\LoaiThuTuc::class, 'procedure_type_id');
    }

    public function xa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Xa::class, 'ward_id');
    }

    public function nguoiThamTra(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'inspector_id');
    }

    public function soTheoDoiGroups()
    {
        return $this->belongsToMany(SoTheoDoiGroup::class, 'ho_so_so_theo_doi');
    }

    public function getTrangThaiMetaAttribute()
    {
        if ($this->status === 'hoan_thanh') {
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

        $daysLeft = now()->diffInDays($this->deadline, false);

        $color = match (true) {
            $daysLeft >= 5   => 'primary',
            $daysLeft >= 3   => 'info',
            $daysLeft === 2  => 'warning',
            $daysLeft === 1  => 'orange',
            $daysLeft <= 0   => 'danger',
            default          => 'secondary',
        };

        return [
            'text'  => $map[$this->status] ?? '—',
            'color' => $color,
        ];
    }

    protected static function booted()
    {
        static::deleting(function ($hoSo) {
            foreach ($hoSo->files as $file) {
                Storage::disk('public')->delete($file->file_path);
            }
            $hoSo->files()->delete();
        });

        static::updating(function ($hoSo) {
            if ($hoSo->isDirty('status')) {
                $hoSo->trangThaiLogs()->create([
                    'old_status'  => $hoSo->getOriginal('status'),
                    'new_status' => $hoSo->status,
                    'user_id'        => auth()->id(),
                ]);
            }
        });
    }
}
