<?php

namespace App\Http\Controllers\XuatFile;

use App\Http\Controllers\Controller;
use App\Models\MauWord;
use App\Models\MauWordFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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

        /* ========== TẠO THƯ MỤC ========== */
        if ($action === 'create_folder') {

            $request->validate([
                'ten' => 'required|string|max:255',
            ]);

            MauWordFolder::create([
                'ten' => $request->ten,
            ]);

            return back()->with('success', 'Tạo thư mục thành công');
        }

        /* ========== UPLOAD WORD ========== */
        if ($action === 'upload_template') {

            $request->validate([
                'ten'       => 'required|string|max:255',
                'file'      => 'required|mimes:doc,docx',
                'folder_id' => 'required|exists:mau_word_folders,id',
            ]);

            $folder = MauWordFolder::findOrFail($request->folder_id);

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();

            $path = $file->storeAs(
                "mau-word/{$folder->id}",
                $fileName
            );

            MauWord::create([
                'ten'       => $request->ten,
                'file_path' => $path,
                'folder_id' => $folder->id,
            ]);

            return back()->with('success', 'Upload mẫu Word thành công');
        }

        return back()->with('error', 'Hành động không hợp lệ');
    }

    public function update(Request $request, MauWord $mauWord)
    {
        $request->validate([
            'ten' => 'required|string|max:255',
        ]);

        $mauWord->update([
            'ten' => $request->ten,
        ]);

        return back()->with('success', 'Cập nhật tên mẫu Word thành công');
    }

    public function destroy(MauWord $mauWord)
    {
        if ($mauWord->file_path && Storage::exists($mauWord->file_path)) {
            Storage::delete($mauWord->file_path);
        }

        $mauWord->delete();

        return back()->with('success', 'Đã xoá mẫu Word');
    }

    public function destroyFolder(MauWordFolder $folder)
    {
        DB::transaction(function () use ($folder) {

            foreach ($folder->mauWords as $mau) {
                if ($mau->file_path && Storage::exists($mau->file_path)) {
                    Storage::delete($mau->file_path);
                }
            }

            Storage::deleteDirectory("mau-word/{$folder->id}");

            $folder->mauWords()->delete();

            $folder->delete();
        });

        return back()->with('success', 'Đã xoá thư mục và toàn bộ mẫu Word bên trong');
    }
}
