<?php

namespace App\Http\Controllers\XuatFile;

use App\Http\Controllers\Controller;
use App\Models\MauWord;
use App\Models\MauWordFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MauWordController extends Controller
{
    public function index()
    {
        $folders = MauWordFolder::with('mauWords')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('xuat-file.word.mau-word', compact('folders'));
    }

    public function store(Request $request)
    {
        $action = $request->input('action');

        // Tạo thư mục
        if ($action === 'create_folder') {
            $request->validate([
                'ten' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('mau_word_folders', 'ten'),
                ],
            ]);

            MauWordFolder::create(['ten' => $request->ten]);

            return back()->with('success', 'Tạo thư mục thành công!');
        }

        // Upload mẫu Word mới
        if ($action === 'upload_template') {
            $request->validate([
                'ten'             => 'required|string|max:255',
                'file'            => 'required|file|mimes:doc,docx|max:10240',
                'folder_id'       => 'required|exists:mau_word_folders,id',
                'ghi_chu'         => 'nullable|string|max:2000',
                'file_dinh_kem'   => 'nullable|file|max:20480',
            ]);

            $folder = MauWordFolder::findOrFail($request->folder_id);

            $wordPath = $request->file('file')->storeAs(
                "mau-word/{$folder->id}",
                time() . '_' . $request->file('file')->getClientOriginalName(),
                'public'
            );

            $attachmentPath = null;
            if ($request->hasFile('file_dinh_kem')) {
                $attachmentPath = $request->file('file_dinh_kem')->storeAs(
                    "mau-word/{$folder->id}/attachments",
                    time() . '_attach_' . $request->file('file_dinh_kem')->getClientOriginalName(),
                    'public'
                );
            }

            MauWord::create([
                'ten'           => $request->ten,
                'file_path'     => $wordPath,
                'ghi_chu'       => $request->ghi_chu,
                'file_dinh_kem' => $attachmentPath,
                'folder_id'     => $folder->id,
            ]);

            return back()->with('success', 'Upload mẫu Word thành công!');
        }

        return back()->with('error', 'Hành động không hợp lệ');
    }

    /**
     * Update CHUNG cho cả Folder và MauWord
     */
    public function update(Request $request, $id)
    {
        $type = $request->input('type');

        if ($type === 'folder') {
            $folder = MauWordFolder::findOrFail($id);

            $request->validate([
                'ten' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('mau_word_folders', 'ten')->ignore($folder->id),
                ],
            ]);

            $folder->update(['ten' => $request->ten]);

            return back()->with('success', 'Cập nhật thư mục thành công!');
        }

        if ($type === 'mauword') {
            $mauWord = MauWord::findOrFail($id);

            $request->validate([
                'ten'             => 'required|string|max:255',
                'ghi_chu'         => 'nullable|string|max:2000',
                'folder_id'       => 'required|exists:mau_word_folders,id',
                'file'            => 'nullable|file|mimes:doc,docx|max:10240',
                'file_dinh_kem'   => 'nullable|file|max:20480',
            ]);

            $oldWordPath       = $mauWord->file_path;
            $oldAttachmentPath = $mauWord->file_dinh_kem;
            $wordPath          = $oldWordPath;
            $attachmentPath    = $oldAttachmentPath;

            try {
                if ($request->hasFile('file')) {
                    $wordPath = $request->file('file')->storeAs(
                        "mau-word/{$request->folder_id}",
                        time() . '_' . $request->file('file')->getClientOriginalName(),
                        'public'
                    );
                }

                if ($request->hasFile('file_dinh_kem')) {
                    $attachmentPath = $request->file('file_dinh_kem')->storeAs(
                        "mau-word/{$request->folder_id}/attachments",
                        time() . '_attach_' . $request->file('file_dinh_kem')->getClientOriginalName(),
                        'public'
                    );
                }

                $mauWord->update([
                    'ten'           => $request->ten,
                    'ghi_chu'       => $request->ghi_chu,
                    'file_path'     => $wordPath,
                    'file_dinh_kem' => $attachmentPath,
                    'folder_id'     => $request->folder_id,
                ]);

                // Xóa file cũ nếu thay mới
                if ($wordPath !== $oldWordPath && $oldWordPath) {
                    Storage::disk('public')->delete($oldWordPath);
                }
                if ($attachmentPath !== $oldAttachmentPath && $oldAttachmentPath) {
                    Storage::disk('public')->delete($oldAttachmentPath);
                }

                return back()->with('success', 'Cập nhật mẫu Word thành công!');
            } catch (\Exception $e) {
                // Rollback nếu lỗi
                if ($wordPath !== $oldWordPath && $wordPath) {
                    Storage::disk('public')->delete($wordPath);
                }
                if ($attachmentPath !== $oldAttachmentPath && $attachmentPath) {
                    Storage::disk('public')->delete($attachmentPath);
                }

                return back()->with('error', 'Cập nhật thất bại: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Loại cập nhật không hợp lệ');
    }

    public function destroy(MauWord $mauWord)
    {
        if ($mauWord->file_path) Storage::disk('public')->delete($mauWord->file_path);
        if ($mauWord->file_dinh_kem) Storage::disk('public')->delete($mauWord->file_dinh_kem);

        $mauWord->delete();

        return back()->with('success', 'Đã xóa mẫu Word');
    }

    public function destroyFolder(MauWordFolder $folder)
    {
        DB::transaction(function () use ($folder) {
            foreach ($folder->mauWords as $mau) {
                if ($mau->file_path) Storage::disk('public')->delete($mau->file_path);
                if ($mau->file_dinh_kem) Storage::disk('public')->delete($mau->file_dinh_kem);
            }
            Storage::disk('public')->deleteDirectory("mau-word/{$folder->id}");
            $folder->mauWords()->delete();
            $folder->delete();
        });

        return back()->with('success', 'Đã xóa thư mục và toàn bộ mẫu bên trong');
    }
}
