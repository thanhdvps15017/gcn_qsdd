<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoSoTrangThaiLog extends Model
{
    protected $fillable = [
        'ho_so_id',
        'old_status',
        'new_status',
        'user_id',
    ];

    public function hoSo(): BelongsTo
    {
        return $this->belongsTo(HoSo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
