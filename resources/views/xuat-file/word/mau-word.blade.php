@extends('welcome')

@section('title', 'Quản lý mẫu Word')

@section('content')
    <div class="container-fluid">
        {{-- TẠO FOLDER --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="bi bi-folder-plus"></i> Tạo thư mục
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('mau-word.store') }}">
                    @csrf
                    <input type="hidden" name="action" value="create_folder">

                    <div class="row g-2">
                        <div class="col-md-10">
                            <input name="ten" class="form-control" placeholder="Tên thư mục" required>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success w-100">Tạo</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- DANH SÁCH FOLDER --}}
        <div class="row g-4">
            @forelse ($folders as $folder)
                <div class="col-lg-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-folder-fill text-warning"></i>
                                {{ $folder->ten }}
                            </h6>

                            <form method="POST" action="{{ route('mau-word.destroy-folder', $folder) }}"
                                onsubmit="return confirm('Xoá thư mục «{{ $folder->ten }}»?\nToàn bộ mẫu Word bên trong sẽ bị xoá!')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>

                        {{-- UPLOAD FILE --}}
                        <div class="card-body border-bottom">
                            <form method="POST" action="{{ route('mau-word.store') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="action" value="upload_template">
                                <input type="hidden" name="folder_id" value="{{ $folder->id }}">

                                <input name="ten" class="form-control mb-2" placeholder="Tên mẫu Word" required>

                                <input type="file" name="file" class="form-control mb-2" accept=".doc,.docx" required>

                                <button class="btn btn-primary w-100 btn-sm">
                                    Upload
                                </button>
                            </form>
                        </div>

                        {{-- FILE LIST --}}
                        @if ($folder->mauWords->count())
                            <ul class="list-group list-group-flush">
                                @foreach ($folder->mauWords as $mau)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>
                                            <i class="bi bi-file-word text-primary"></i>
                                            {{ $mau->ten }}
                                        </span>
                                        <form method="POST" action="{{ route('mau-word.destroy', $mau) }}"
                                            onsubmit="return confirm('Xóa mẫu này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center text-muted py-3">
                                Chưa có mẫu Word
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">
                    Chưa có thư mục nào
                </div>
            @endforelse
        </div>
    </div>
@endsection
