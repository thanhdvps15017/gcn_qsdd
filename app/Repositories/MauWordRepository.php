<?php

namespace App\Repositories;

use App\Models\MauWord;
use App\Models\MauWordFolder;

class MauWordRepository
{
    public function getFolders() { 
        return MauWordFolder::with('mauWords')->orderBy('created_at', 'desc')->get(); 
    }
    public function createFolder($data) { 
        return MauWordFolder::create($data); 
    }
    public function createWord($data) { 
        return MauWord::create($data); 
    }
    public function getFolder($id) { 
        return MauWordFolder::findOrFail($id); 
    }
    public function getWord($id) { 
        return MauWord::findOrFail($id); 
    }
    public function updateFolder(MauWordFolder $folder, $data) { 
        return $folder->update($data); 
    }
    public function updateWord(MauWord $word, $data) { 
        return $word->update($data); 
    }
    public function deleteWord(MauWord $word) { 
        return $word->delete(); 
    }
    public function deleteFolder(MauWordFolder $folder) { 
        $folder->mauWords()->delete();
        $folder->delete(); 
    }
}
