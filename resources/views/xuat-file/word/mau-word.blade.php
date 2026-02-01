@extends('welcome')

@section('title', 'Quản lý mẫu Word')

@section('content')
    <div class="container-fluid py-4">

        <!-- Tạo thư mục -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="bi bi-folder-plus me-2"></i> Tạo thư mục mới
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('mau-word.store') }}">
                    @csrf
                    <input type="hidden" name="action" value="create_folder">
                    <div class="input-group">
                        <input name="ten" class="form-control" placeholder="Tên thư mục..." required>
                        <button class="btn btn-success" type="submit">Tạo</button>
                    </div>
                    @error('ten')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </form>
            </div>
        </div>

        <!-- Danh sách thư mục -->
        <div class="row g-4">
            @forelse ($folders as $folder)
                <div class="col-lg-4 col-md-6">
                    <div class="card shadow-sm h-100">
                        <!-- Header thư mục -->
                        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h6 class="mb-0 fw-bold text-truncate" style="max-width: 60%;">
                                <i class="bi bi-folder-fill text-warning me-2"></i>
                                {{ $folder->ten }}
                            </h6>
                            <div class="d-flex gap-2 flex-nowrap">
                                <!-- NÚT SỬA THƯ MỤC -->
                                <button class="btn btn-sm btn-outline-warning btn-edit-folder" data-id="{{ $folder->id }}"
                                    data-ten="{{ addslashes($folder->ten) }}">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </button>

                                <form method="POST" action="{{ route('mau-word.destroy-folder', $folder) }}"
                                    onsubmit="return confirm('Xóa thư mục này sẽ xóa hết mẫu bên trong. Tiếp tục?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Upload mẫu mới -->
                        <div class="card-body border-bottom">
                            <form method="POST" action="{{ route('mau-word.store') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="action" value="upload_template">
                                <input type="hidden" name="folder_id" value="{{ $folder->id }}">

                                <div class="mb-3">
                                    <input name="ten" class="form-control" placeholder="Tên mẫu Word" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">File Word (.doc, .docx)</label>
                                    <input type="file" name="file" class="form-control" accept=".doc,.docx" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">Ghi chú</label>
                                    <textarea name="ghi_chu" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">File đính kèm</label>
                                    <input type="file" name="file_dinh_kem" class="form-control"
                                        accept=".pdf,.docx,.xlsx,.jpg,.png,.zip">
                                </div>
                                <button class="btn btn-primary w-100 btn-sm">Upload mẫu</button>
                            </form>
                        </div>

                        <!-- Danh sách mẫu -->
                        @if ($folder->mauWords->isNotEmpty())
                            <ul class="list-group list-group-flush">
                                @foreach ($folder->mauWords as $mau)
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div class="flex-grow-1">
                                                <strong>
                                                    <i class="bi bi-file-earmark-word-fill text-primary me-1"></i>
                                                    {{ $mau->ten }}
                                                </strong>
                                                @if ($mau->ghi_chu)
                                                    <div class="small text-secondary mt-1">
                                                        {{ Str::limit($mau->ghi_chu, 80) }}
                                                    </div>
                                                @endif
                                                @if ($mau->file_dinh_kem)
                                                    <div class="small mt-1">
                                                        <a href="{{ Storage::url($mau->file_dinh_kem) }}" target="_blank"
                                                            class="text-info">
                                                            <i class="bi bi-paperclip"></i> File đính kèm
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- NÚT SỬA + XÓA MẪU WORD -->
                                            <div class="d-flex gap-2 flex-nowrap">
                                                <button class="btn btn-sm btn-outline-warning btn-edit-mau"
                                                    data-id="{{ $mau->id }}" data-ten="{{ addslashes($mau->ten) }}"
                                                    data-ghichu="{{ addslashes($mau->ghi_chu ?? '') }}"
                                                    data-folder-id="{{ $mau->folder_id }}">
                                                    <i class="bi bi-pencil-square"></i> Sửa
                                                </button>

                                                <form method="POST" action="{{ route('mau-word.destroy', $mau) }}"
                                                    onsubmit="return confirm('Xóa mẫu Word này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                                        <i class="bi bi-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center text-muted py-3">Chưa có mẫu Word nào</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    Chưa có thư mục nào. Hãy tạo thư mục đầu tiên.
                </div>
            @endforelse
        </div>

        <!-- === Modal sửa thư mục === -->
        <div class="modal fade" id="editFolderModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title text-dark">Chỉnh sửa thư mục</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" id="editFolderForm">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" name="type" value="folder">
                            <input type="hidden" name="id" id="folder_id_hidden">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên thư mục</label>
                                <input type="text" name="ten" id="folder_ten" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-warning">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- === Modal sửa mẫu Word === -->
        <div class="modal fade" id="editMauModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Chỉnh sửa mẫu Word</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" id="editMauForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" name="type" value="mauword">
                            <input type="hidden" name="id" id="mau_id_hidden">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tên mẫu</label>
                                    <input type="text" name="ten" id="mau_ten" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Thư mục</label>
                                    <select name="folder_id" id="mau_folder_id" class="form-select" required>
                                        @foreach ($folders as $f)
                                            <option value="{{ $f->id }}">{{ $f->ten }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Ghi chú</label>
                                    <textarea name="ghi_chu" id="mau_ghi_chu" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Thay file Word</label>
                                    <input type="file" name="file" class="form-control" accept=".doc,.docx">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Thay file đính kèm</label>
                                    <input type="file" name="file_dinh_kem" class="form-control"
                                        accept=".pdf,.docx,.xlsx,.jpg,.png,.zip">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Sửa thư mục
            document.querySelectorAll('.btn-edit-folder').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('folder_id_hidden').value = this.dataset.id;
                    document.getElementById('folder_ten').value = this.dataset.ten;

                    // Sửa route để khớp với route đã khai báo
                    document.getElementById('editFolderForm').action =
                        '{{ route('mau-word.update', '') }}' + '/' + this.dataset.id;

                    new bootstrap.Modal(document.getElementById('editFolderModal')).show();
                });
            });

            // Sửa mẫu Word
            document.querySelectorAll('.btn-edit-mau').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('mau_id_hidden').value = this.dataset.id;
                    document.getElementById('mau_ten').value = this.dataset.ten;
                    document.getElementById('mau_ghi_chu').value = this.dataset.ghichu || '';
                    document.getElementById('mau_folder_id').value = this.dataset.folderId;

                    // Sửa route để khớp
                    document.getElementById('editMauForm').action =
                        '{{ route('mau-word.update', '') }}' + '/' + this.dataset.id;

                    new bootstrap.Modal(document.getElementById('editMauModal')).show();
                });
            });

        });
    </script>
@endpush
