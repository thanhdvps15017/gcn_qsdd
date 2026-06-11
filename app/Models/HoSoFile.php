<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class HoSoFile extends Model
{
    use HasFactory;
    protected $table = "ho_so_files";
    protected $fillable = [
        'ho_so_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'notes',
    ];

    public function hoSo(): BelongsTo
    {
        return $this->belongsTo(HoSo::class);
    }
}
