<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MauWordFolder extends Model
{
    use HasFactory;

    protected $table = "mau_word_folders";

    protected $fillable = ['ten'];

    public function mauWords()
    {
        return $this->hasMany(MauWord::class, 'folder_id');
    }
}
