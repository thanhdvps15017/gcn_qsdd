<?php

namespace App\Services;

use App\Repositories\MauWordRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\MauWordFolder;
use App\Models\MauWord;

class MauWordService
{
    protected $repo;

    public function __construct(MauWordRepository $repo) { 
        $this->repo = $repo; 
    }

    public function getFolders() { 
        return $this->repo->getFolders(); 
    }

    public function createFolder($data) { 
        return $this->repo->createFolder($data); 
    }

    public function uploadTemplate($data, $file, $attachment) {
        $folder = $this->repo->getFolder($data['folder_id']);
        
        $wordPath = $file->storeAs("mau-word/{$folder->id}", time() . '_' . $file->getClientOriginalName(), 'public');
        
        $attachmentPath = null;
        if ($attachment) {
            $attachmentPath = $attachment->storeAs("mau-word/{$folder->id}/attachments", time() . '_attach_' . $attachment->getClientOriginalName(), 'public');
        }
        
        return $this->repo->createWord([
            'name' => $data['name'], 
            'file_path' => $wordPath, 
            'notes' => $data['notes'] ?? null,
            'attachment' => $attachmentPath, 
            'folder_id' => $folder->id
        ]);
    }

    public function updateFolder($id, $data) {
        $folder = $this->repo->getFolder($id);
        $this->repo->updateFolder($folder, $data);
        return $folder;
    }

    public function updateWord($id, $data, $file, $attachment) {
        $word = $this->repo->getWord($id);
        $oldWordPath = $word->file_path;
        $oldAttachmentPath = $word->attachment;
        $wordPath = $oldWordPath;
        $attachmentPath = $oldAttachmentPath;

        try {
            if ($file) {
                $wordPath = $file->storeAs("mau-word/{$data['folder_id']}", time() . '_' . $file->getClientOriginalName(), 'public');
            }
            if ($attachment) {
                $attachmentPath = $attachment->storeAs("mau-word/{$data['folder_id']}/attachments", time() . '_attach_' . $attachment->getClientOriginalName(), 'public');
            }
            
            $this->repo->updateWord($word, [
                'name' => $data['name'], 
                'notes' => $data['notes'] ?? null,
                'file_path' => $wordPath, 
                'attachment' => $attachmentPath, 
                'folder_id' => $data['folder_id']
            ]);

            if ($wordPath !== $oldWordPath && $oldWordPath) {
                Storage::disk('public')->delete($oldWordPath);
            }
            if ($attachmentPath !== $oldAttachmentPath && $oldAttachmentPath) {
                Storage::disk('public')->delete($oldAttachmentPath);
            }
        } catch (\Exception $e) {
            if ($wordPath !== $oldWordPath && $wordPath) Storage::disk('public')->delete($wordPath);
            if ($attachmentPath !== $oldAttachmentPath && $attachmentPath) Storage::disk('public')->delete($attachmentPath);
            throw $e;
        }
    }

    public function destroyWord(MauWord $word) {
        if ($word->file_path) Storage::disk('public')->delete($word->file_path);
        if ($word->attachment) Storage::disk('public')->delete($word->attachment);
        $this->repo->deleteWord($word);
    }

    public function destroyFolder(MauWordFolder $folder) {
        DB::transaction(function () use ($folder) {
            foreach ($folder->mauWords as $mau) {
                if ($mau->file_path) Storage::disk('public')->delete($mau->file_path);
                if ($mau->attachment) Storage::disk('public')->delete($mau->attachment);
            }
            Storage::disk('public')->deleteDirectory("mau-word/{$folder->id}");
            $this->repo->deleteFolder($folder);
        });
    }
}
