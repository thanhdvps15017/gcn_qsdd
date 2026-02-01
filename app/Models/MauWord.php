<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MauWord extends Model
{
    use HasFactory;

    protected $table = "mau_words";

    protected $fillable = [
        'ten',
        'file_path',
        'folder_id',
        'ghi_chu',
        'file_dinh_kem',
    ];

    public function folder()
    {
        return $this->belongsTo(MauWordFolder::class, 'folder_id');
    }
}
