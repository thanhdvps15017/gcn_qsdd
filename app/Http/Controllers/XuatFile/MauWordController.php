<?php

namespace App\Http\Controllers\XuatFile;

use App\Http\Controllers\Controller;
use App\Models\MauWord;
use App\Models\MauWordFolder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\MauWordService;

class MauWordController extends Controller
{
    protected $service;

    public function __construct(MauWordService $service) {
        $this->service = $service;
    }

    public function index() {
        $folders = $this->service->getFolders();
        return view('cai-dat.word.mau-word', compact('folders'));
    }

    public function store(Request $request) {
        $action = $request->input('action');

        if ($action === 'create_folder') {
            $request->validate([
                'name' => [ 'required', 'string', 'max:255', Rule::unique('mau_word_folders', 'name') ],
            ]);
            $this->service->createFolder(['name' => $request->name]);
            return back()->with('success', 'Tạo thư mục thành công!');
        }

        if ($action === 'upload_template') {
            $request->validate([
                'name'             => 'required|string|max:255',
                'file'            => 'required|file|mimes:doc,docx|max:10240',
                'folder_id'       => 'required|exists:mau_word_folders,id',
                'notes'         => 'nullable|string|max:2000',
                'attachment'   => 'nullable|file|max:20480',
            ]);

            $this->service->uploadTemplate(
                $request->only(['name', 'folder_id', 'notes']), 
                $request->file('file'), 
                $request->file('attachment')
            );

            return back()->with('success', 'Upload mẫu Word thành công!');
        }

        return back()->with('error', 'Hành động không hợp lệ');
    }

    public function update(Request $request, $id) {
        $type = $request->input('type');

        if ($type === 'folder') {
            $request->validate([
                'name' => [ 'required', 'string', 'max:255', Rule::unique('mau_word_folders', 'name')->ignore($id) ],
            ]);
            $this->service->updateFolder($id, ['name' => $request->name]);
            return back()->with('success', 'Cập nhật thư mục thành công!');
        }

        if ($type === 'mauword') {
            $request->validate([
                'name'             => 'required|string|max:255',
                'notes'         => 'nullable|string|max:2000',
                'folder_id'       => 'required|exists:mau_word_folders,id',
                'file'            => 'nullable|file|mimes:doc,docx|max:10240',
                'attachment'   => 'nullable|file|max:20480',
            ]);

            try {
                $this->service->updateWord(
                    $id, 
                    $request->only(['name', 'folder_id', 'notes']), 
                    $request->file('file'), 
                    $request->file('attachment')
                );
                return back()->with('success', 'Cập nhật mẫu Word thành công!');
            } catch (\Exception $e) {
                return back()->with('error', 'Cập nhật thất bại: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Loại cập nhật không hợp lệ');
    }

    public function destroy(MauWord $mauWord) {
        $this->service->destroyWord($mauWord);
        return back()->with('success', 'Đã xóa mẫu Word');
    }

    public function destroyFolder(MauWordFolder $folder) {
        $this->service->destroyFolder($folder);
        return back()->with('success', 'Đã xóa thư mục và toàn bộ mẫu bên trong');
    }
}
