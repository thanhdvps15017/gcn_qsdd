<?php

namespace App\Http\Controllers\XuatFile;

use App\Http\Controllers\Controller;
use App\Models\MauWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MauWordController extends Controller
{
    public function index()
    {
        $mauWords = MauWord::latest()->get();
        return view('xuat-file.word.mau-word', compact('mauWords'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten'  => 'required|string|max:255',
            'file' => 'required',
        ]);

        $path = $request->file('file')->store('word-templates');

        MauWord::create([
            'ten'       => $request->ten,
            'file_path' => $path,
        ]);

        return back()->with('success', 'Upload mẫu Word thành công');
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
}
